<?php

declare(strict_types=1);

/** Return the executable tokens, ignoring formatting and comments. */
function releasePhpTokens(array $tokens): array
{
    $result = [];
    foreach ($tokens as $token) {
        if (is_array($token)) {
            if (in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) continue;
            $result[] = [$token[0], $token[0] === T_OPEN_TAG ? rtrim($token[1]) : $token[1]];
        } else {
            $result[] = $token;
        }
    }
    return $result;
}

/** Minify only the disposable release tree, never the source checkout. */
function minifyReleasePhp(string $packageRoot): array
{
    $root = realpath($packageRoot);
    if ($root === false || basename($root) !== 'card-system' ||
        !is_file(dirname($root) . '/.card-system-build-owner')) {
        throw new RuntimeException('PHP minification requires an owned temporary release tree.');
    }

    $stats = ['files' => 0, 'before' => 0, 'after' => 0,
        'vendor_files' => 0, 'vendor_before' => 0, 'vendor_after' => 0];
    $excluded = ['app/Library/Gateway', 'node_modules', 'tests', 'build', 'storage', '.git',
        // Nette reads PHPDoc at runtime for schema types and magic properties.
        'vendor/nette'];
    $filter = new RecursiveCallbackFilterIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
        static function (SplFileInfo $file) use ($root, $excluded): bool {
            if ($file->isLink()) return false;
            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
            return !in_array($relative, $excluded, true) && $file->getFilename() !== 'node_modules';
        }
    );
    foreach (new RecursiveIteratorIterator($filter) as $file) {
        if (!$file->isFile() || ($file->getExtension() !== 'php' && $file->getFilename() !== 'artisan') ||
            str_ends_with($file->getFilename(), '.blade.php')) continue;
        $path = $file->getPathname();
        $source = file_get_contents($path);
        if ($source === false) throw new RuntimeException('Cannot read PHP file: ' . $path);
        // php_strip_whitespace also removes executable CLI shebang headers.
        if (str_starts_with($source, '#!')) continue;
        $tokens = token_get_all($source);
        $types = array_column(array_filter($tokens, 'is_array'), 0);
        // Moving lines or an embedded payload would change these files' behavior.
        if (in_array(T_LINE, $types, true) || in_array(T_HALT_COMPILER, $types, true) ||
            (!in_array(T_OPEN_TAG, $types, true) && !in_array(T_OPEN_TAG_WITH_ECHO, $types, true))) continue;
        $minified = php_strip_whitespace($path);
        if ($minified === '' || releasePhpTokens($tokens) !== releasePhpTokens(token_get_all($minified))) {
            throw new RuntimeException('PHP minification changed executable tokens: ' . $path);
        }
        if (strlen($minified) >= strlen($source)) continue;
        if (file_put_contents($path, $minified) !== strlen($minified)) {
            throw new RuntimeException('Cannot write minified PHP file: ' . $path);
        }
        $stats['files']++;
        $stats['before'] += strlen($source);
        $stats['after'] += strlen($minified);
        $relative = str_replace('\\', '/', substr($path, strlen($root) + 1));
        if (str_starts_with($relative, 'vendor/')) {
            $stats['vendor_files']++;
            $stats['vendor_before'] += strlen($source);
            $stats['vendor_after'] += strlen($minified);
        }
    }
    return $stats;
}
