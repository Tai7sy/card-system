<?php

declare(strict_types=1);

require_once __DIR__ . '/minify-php.php';

// Build current source files, including uncommitted changes, in a disposable tree.
// Git-ignored files stay excluded. No application boot or database connection is needed.
$root = dirname(__DIR__);
$temporaryRoot = null;
$pendingArchive = null;
$pendingVersion = null;

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

function copyWorkingTree(string $source, string $destination, array $excluded = []): void
{
    $files = command(['git', 'ls-files', '--cached', '--others', '--exclude-standard', '-z'], $source, true);
    foreach (array_unique(explode("\0", $files)) as $relative) {
        if ($relative === '' || in_array($relative, $excluded, true)) continue;
        $path = $source . '/' . $relative;
        if (is_link($path)) throw new RuntimeException('Release source must not be a symbolic link: ' . $relative);
        if (!file_exists($path)) continue; // Respect deletions in the working tree.
        if (!is_file($path)) throw new RuntimeException('Unexpected source directory: ' . $relative);
        $target = $destination . '/' . $relative;
        if (!is_dir(dirname($target)) && !mkdir(dirname($target), 0755, true)) {
            throw new RuntimeException('Cannot create source directory: ' . dirname($target));
        }
        if (!copy($path, $target)) throw new RuntimeException('Cannot copy release source: ' . $relative);
    }
}

try {
    $revision = command(['git', 'rev-parse', 'HEAD'], $root, true);
    $submodule = 'app/Library/Gateway';
    $gatewayPath = $root . '/' . $submodule;
    if (!file_exists($gatewayPath . '/.git') || !is_file($gatewayPath . '/Pay/Pay.php')) {
        throw new RuntimeException('Initialize the Gateway submodule before building.');
    }

    $temporaryRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'card-system-build-' . bin2hex(random_bytes(8));
    if (!mkdir($temporaryRoot, 0700)) throw new RuntimeException('Cannot create temporary build directory.');
    file_put_contents($temporaryRoot . '/.card-system-build-owner', $revision);
    $packageRoot = $temporaryRoot . '/card-system';
    mkdir($packageRoot, 0755);
    echo "Preparing current working tree (base commit $revision)...\n";
    copyWorkingTree($root, $packageRoot, [$submodule]);
    copyWorkingTree($gatewayPath, $packageRoot . '/' . $submodule);

    $versionFile = json_decode(file_get_contents($packageRoot . '/.version'), true, 512, JSON_THROW_ON_ERROR);
    $provider = file_get_contents($packageRoot . '/app/Providers/ConfigServiceProvider.php');
    if (!preg_match('/[\'\"]app\.version[\'\"]\s*=>\s*[\'\"]([0-9]+(?:\.[0-9]+)*)[\'\"]/', $provider, $match)) {
        throw new RuntimeException('Invalid app.version in ConfigServiceProvider.php.');
    }
    $version = $match[1];
    $archiveName = 'card_release.tar.gz';
    $versionFile['version'] = $version;
    $versionFile['data']['version'] = $version;
    $versionFile['url'] = preg_replace('~(/releases/download/)[^/]+/[^/?#]+~',
        '${1}' . $version . '/' . $archiveName, $versionFile['url'], 1, $urlReplacements);
    if ($urlReplacements !== 1) throw new RuntimeException('Invalid release download URL in .version.');
    echo "Release version: $version\n";

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

    $archivePath = $temporaryRoot . '/' . $archiveName;
    echo "Packing $archiveName...\n";
    command([
        'tar', '-czf', $archivePath,
        '--exclude=node_modules', '--exclude=.git', '--exclude=.env', '--exclude=.env.local',
        '--exclude=.env.*.local', '--exclude=card-system/build', '--exclude=card-system/tests',
        '--exclude=card-system/.github',
        // The release index describes this archive and cannot contain its own digest.
        '--exclude=card-system/.version',
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
    $md5 = md5_file($pendingArchive);
    if ($md5 === false) throw new RuntimeException('Cannot calculate release MD5.');
    $versionFile['md5'] = $md5;
    $versionJson = json_encode($versionFile, JSON_THROW_ON_ERROR) . "\n";
    $pendingVersion = $root . '/.version.tmp-' . bin2hex(random_bytes(4));
    if (file_put_contents($pendingVersion, $versionJson) !== strlen($versionJson)) {
        throw new RuntimeException('Cannot stage release metadata.');
    }
    if (!rename($pendingArchive, $destination)) throw new RuntimeException('Cannot publish completed release archive.');
    $pendingArchive = null;
    if (!rename($pendingVersion, $root . '/.version')) throw new RuntimeException('Cannot update repository .version.');
    $pendingVersion = null;
    echo "Release: $destination\nMD5: $md5\nUpdated: $root/.version\n";
} catch (Throwable $error) {
    file_put_contents('php://stderr', 'Build failed: ' . $error->getMessage() . "\n");
    $failed = true;
} finally {
    if ($pendingArchive !== null && is_file($pendingArchive)) unlink($pendingArchive);
    if ($pendingVersion !== null && is_file($pendingVersion)) unlink($pendingVersion);
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
