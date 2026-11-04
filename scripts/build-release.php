<?php

declare(strict_types=1);

require_once __DIR__ . '/minify-php.php';

// Build from a committed tree so local configuration and untracked files never
// become part of a release. No application boot or database connection is needed.
$root = dirname(__DIR__);
$temporaryRoot = null;
$pendingArchive = null;

function command(array $arguments, string $directory, bool $capture = false): string
{
    $process = proc_open($arguments, [0 => ['file', PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null', 'r'], 1 => ['pipe', 'w'], 2 => ['redirect', 1]], $pipes, $directory);
    if (!is_resource($process)) {
        throw new RuntimeException('Could not start: ' . $arguments[0]);
    }
    $output = '';
    while (!feof($pipes[1])) {
        $chunk = fread($pipes[1], 8192);
        if ($chunk === false) break;
        if ($capture) $output .= $chunk;
        else echo $chunk;
    }
    fclose($pipes[1]);
    $exitCode = proc_close($process);
    if ($exitCode !== 0) {
        throw new RuntimeException(sprintf('%s failed (exit %d).', $arguments[0], $exitCode));
    }
    return trim($output);
}

function cleanTemporaryTree(string $directory, string $ownedRoot): void
{
    $realRoot = realpath($ownedRoot);
    $realDirectory = realpath($directory);
    if ($realRoot === false || $realDirectory === false ||
        !is_file($ownedRoot . '/.card-system-build-owner') ||
        ($realDirectory !== $realRoot && !str_starts_with($realDirectory, $realRoot . DIRECTORY_SEPARATOR))) {
        throw new RuntimeException('Refusing to clean a directory outside the owned build workspace.');
    }
    foreach (new DirectoryIterator($directory) as $entry) {
        if ($entry->isDot()) continue;
        if ($realDirectory === $realRoot && $entry->getFilename() === '.card-system-build-owner') continue;
        $path = $entry->getPathname();
        if ($entry->isDir() && !$entry->isLink()) {
            cleanTemporaryTree($path, $ownedRoot);
        } else {
            if (!is_writable($path)) @chmod($path, 0600);
            if (!unlink($path)) throw new RuntimeException('Cannot remove temporary file: ' . $path);
        }
    }
    if ($realDirectory === $realRoot) unlink($ownedRoot . '/.card-system-build-owner');
    if (!rmdir($directory)) throw new RuntimeException('Cannot remove temporary directory: ' . $directory);
}

try {
    if (command(['git', 'status', '--porcelain', '--untracked-files=no'], $root, true) !== '') {
        throw new RuntimeException('Commit tracked changes before building a release. Local untracked files are excluded.');
    }
    $revision = command(['git', 'rev-parse', 'HEAD'], $root, true);
    $submodule = 'app/Library/Gateway';
    $entry = command(['git', 'ls-tree', 'HEAD', '--', $submodule], $root, true);
    if (!preg_match('/^160000 commit ([a-f0-9]{40})\s/', $entry, $match)) {
        throw new RuntimeException('The Gateway submodule is missing from the committed tree.');
    }
    $gatewayRevision = $match[1];
    $gatewayPath = $root . '/' . $submodule;
    if (!file_exists($gatewayPath . '/.git') ||
        command(['git', 'rev-parse', 'HEAD'], $gatewayPath, true) !== $gatewayRevision ||
        command(['git', 'status', '--porcelain'], $gatewayPath, true) !== '') {
        throw new RuntimeException('Initialize the pinned Gateway submodule and keep its working tree clean.');
    }

    $temporaryRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'card-system-build-' . bin2hex(random_bytes(8));
    if (!mkdir($temporaryRoot, 0700)) throw new RuntimeException('Cannot create temporary build directory.');
    file_put_contents($temporaryRoot . '/.card-system-build-owner', $revision);
    $packageRoot = $temporaryRoot . '/card-system';
    mkdir($packageRoot, 0755);
    echo "Preparing committed source $revision\n";
    command(['git', 'archive', '--format=tar', '--output=' . $temporaryRoot . '/source.tar', $revision], $root);
    command(['tar', '-xf', $temporaryRoot . '/source.tar', '-C', $packageRoot], $root);
    if (!is_dir($packageRoot . '/' . $submodule)) mkdir($packageRoot . '/' . $submodule, 0755, true);
    command(['git', '-c', 'core.autocrlf=false', '-c', 'core.eol=lf', 'archive', '--format=tar', '--output=' . $temporaryRoot . '/gateway.tar', $gatewayRevision], $gatewayPath);
    command(['tar', '-xf', $temporaryRoot . '/gateway.tar', '-C', $packageRoot . '/' . $submodule], $root);

    $versionFile = json_decode(file_get_contents($packageRoot . '/.version'), true, 512, JSON_THROW_ON_ERROR);
    $version = $versionFile['version'] ?? '';
    if (!preg_match('/\A[0-9]+(?:\.[0-9]+)*\z/', $version)) throw new RuntimeException('Invalid release version.');

    $composerBinary = getenv('COMPOSER_BINARY');
    $composer = $composerBinary && is_file($composerBinary)
        ? [PHP_BINARY, $composerBinary]
        : [PHP_OS_FAMILY === 'Windows' ? 'composer.bat' : 'composer'];
    $npm = ['npm'];
    if (PHP_OS_FAMILY === 'Windows') {
        $node = strtok(command(['where.exe', 'node.exe'], $root, true), "\r\n");
        $npmShim = strtok(command(['where.exe', 'npm.cmd'], $root, true), "\r\n");
        $npmCli = dirname($npmShim) . '/node_modules/npm/bin/npm-cli.js';
        if (!is_file($npmCli)) $npmCli = dirname($node) . '/node_modules/npm/bin/npm-cli.js';
        if (!is_file($npmCli)) throw new RuntimeException('Cannot locate the npm CLI alongside Node.js or npm.cmd.');
        $npm = [$node, $npmCli];
    }
    echo "Validating locked PHP dependencies...\n";
    command([...$composer, 'validate', '--no-check-publish'], $packageRoot);
    echo "Installing production PHP dependencies...\n";
    command([...$composer, 'install', '--no-dev', '--prefer-dist', '--no-interaction', '--optimize-autoloader'], $packageRoot);
    echo "Installing frontend dependencies and building both applications...\n";
    command([...$npm, 'ci', '--no-audit', '--no-fund'], $packageRoot);
    command([...$npm, 'run', 'build'], $packageRoot);

    foreach (glob($packageRoot . '/bootstrap/cache/*.php') as $cacheFile) unlink($cacheFile);
    // Keep the writable directory in the archive, without build-host log files.
    $logsDirectory = $packageRoot . '/storage/logs';
    if (is_dir($logsDirectory)) cleanTemporaryTree($logsDirectory, $temporaryRoot);
    if (!mkdir($logsDirectory, 0755, true)) throw new RuntimeException('Cannot create release log directory.');
    foreach (['public/dist/admin', 'public/dist/shop'] as $directory) {
        if (!glob($packageRoot . '/' . $directory . '/js/*.js')) {
            throw new RuntimeException('Missing frontend output: ' . $directory);
        }
    }
    foreach (['resources/views/admin.blade.php', 'resources/views/shop_theme/Material/index.blade.php', 'vendor/autoload.php'] as $required) {
        if (!is_file($packageRoot . '/' . $required)) throw new RuntimeException('Missing release file: ' . $required);
    }

    echo "Minifying release PHP, including production vendor dependencies...\n";
    $minification = minifyReleasePhp($packageRoot);
    echo sprintf("PHP: %d files, %.2f MiB -> %.2f MiB. Vendor: %.2f MiB -> %.2f MiB.\n",
        $minification['files'], $minification['before'] / 1048576, $minification['after'] / 1048576,
        $minification['vendor_before'] / 1048576, $minification['vendor_after'] / 1048576);

    $archiveName = 'card-system-' . $version . '.tar.gz';
    $archivePath = $temporaryRoot . '/' . $archiveName;
    echo "Packing $archiveName...\n";
    command([
        'tar', '-czf', $archivePath,
        '--exclude=node_modules', '--exclude=.git', '--exclude=.env', '--exclude=.env.local',
        '--exclude=.env.*.local', '--exclude=card-system/build', '--exclude=card-system/tests',
        '--exclude=card-system/.github',
        '--exclude=card-system/storage/framework/testing', 'card-system',
    ], $temporaryRoot);
    $entries = command(['tar', '-tzf', $archivePath], $root, true);
    if (preg_match('~(?:^|/)(?:node_modules|\.git)(?:/|$)|(?:^|/)\.env(?:\r?\n|$)~m', $entries)) {
        throw new RuntimeException('Release contains a prohibited runtime or local configuration path.');
    }

    $releaseDirectory = $root . '/build/releases';
    if (!is_dir($releaseDirectory) && !mkdir($releaseDirectory, 0755, true)) throw new RuntimeException('Cannot create release output directory.');
    $realReleaseDirectory = realpath($releaseDirectory);
    $realRoot = realpath($root);
    if (!str_starts_with(strtolower($realReleaseDirectory), strtolower($realRoot . DIRECTORY_SEPARATOR))) {
        throw new RuntimeException('Release output directory must stay inside the repository.');
    }
    $destination = $releaseDirectory . '/' . $archiveName;
    $pendingArchive = $destination . '.tmp-' . bin2hex(random_bytes(4));
    if (!copy($archivePath, $pendingArchive)) throw new RuntimeException('Cannot stage release archive.');
    if (!rename($pendingArchive, $destination)) throw new RuntimeException('Cannot publish completed release archive.');
    $pendingArchive = null;
    echo "Release: $destination\nSHA256: " . hash_file('sha256', $destination) . "\n";
} catch (Throwable $error) {
    file_put_contents('php://stderr', 'Build failed: ' . $error->getMessage() . "\n");
    $failed = true;
} finally {
    if ($pendingArchive !== null && is_file($pendingArchive)) unlink($pendingArchive);
    if ($temporaryRoot !== null && is_dir($temporaryRoot)) {
        try {
            cleanTemporaryTree($temporaryRoot, $temporaryRoot);
            echo "Temporary build directory removed.\n";
        } catch (Throwable $error) {
            file_put_contents('php://stderr', 'Cleanup failed: ' . $error->getMessage() . "\n");
            $failed = true;
        }
    }
}
exit(isset($failed) ? 1 : 0);
