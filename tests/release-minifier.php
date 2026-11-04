<?php

declare(strict_types=1);

require dirname(__DIR__) . '/scripts/minify-php.php';

$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    if (!$condition) throw new RuntimeException($message);
    $checks++;
};
$temporaryRoot = sys_get_temp_dir() . '/card-system-minifier-test-' . bin2hex(random_bytes(8));
mkdir($temporaryRoot, 0700);
file_put_contents($temporaryRoot . '/.card-system-build-owner', 'minifier-test');
$package = $temporaryRoot . '/card-system';
mkdir($package);
$write = static function (string $relative, string $source) use ($package): void {
    $path = $package . '/' . $relative;
    if (!is_dir(dirname($path))) mkdir(dirname($path), 0755, true);
    file_put_contents($path, $source);
};

try {
    $source = <<<'PHP'
<?php
// This ordinary comment must disappear.
/** This documentation comment must disappear. */
$name = 'example';
$heredoc = <<<TEXT
    Literal // comment and /* text */
    Interpolated {$name}
    TEXT;
$nowdoc = <<<'TEXT'
    Keep   these spaces and $name.
    TEXT;
return [$heredoc, $nowdoc, 'a' . /* separator */ 'b', '1' . '2', "https://example.test/a  b"];
PHP;
    $write('app/Example.php', $source);
    $write('vendor/example/Example.php', $source);
    $untouched = [
        'app/Library/Gateway/Example.php' => $source,
        'resources/views/example.blade.php' => '<p>  @php /* template */ echo "kept"; @endphp </p>',
        'vendor/nette/utils/Example.php' => "<?php\n/** @property int \$value */\nclass Example {}\n",
        'vendor/example/line.php' => "<?php\n// Keep original line numbers.\nreturn __LINE__;\n",
        'vendor/example/payload.php' => "<?php __halt_compiler();\nraw\x00payload",
        'vendor/example/LICENSE' => 'Example license notice',
        'public/cli.php' => "#!/usr/bin/env php\n<?php return 1;\n",
        'node_modules/example/fixture.php' => $source,
    ];
    foreach ($untouched as $path => $contents) $write($path, $contents);
    $stats = minifyReleasePhp($package);
    $assert($stats['files'] === 2 && $stats['vendor_files'] === 1, 'Wrong compression scope');
    $assert($stats['after'] < $stats['before'], 'PHP was not compressed');
    $minified = file_get_contents($package . '/app/Example.php');
    $assert(eval(substr($source, 5)) === eval(substr($minified, 5)), 'Strings or heredoc behavior changed');
    $assert(!str_contains($minified, 'must disappear'), 'PHP comments were retained');
    foreach ($untouched as $path => $contents) {
        $assert(file_get_contents($package . '/' . $path) === $contents, 'Protected file changed: ' . $path);
    }
    mkdir($temporaryRoot . '/unowned');
    mkdir($temporaryRoot . '/unowned/card-system');
    $refused = false;
    try {
        minifyReleasePhp($temporaryRoot . '/unowned/card-system');
    } catch (RuntimeException $error) {
        $refused = true;
    }
    $assert($refused, 'An unowned source directory was accepted');
    echo "Release PHP minification: $checks checks passed.\n";
} finally {
    $entries = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($temporaryRoot, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($entries as $entry) {
        if ($entry->isDir() && !$entry->isLink()) rmdir($entry->getPathname());
        else unlink($entry->getPathname());
    }
    rmdir($temporaryRoot);
}
