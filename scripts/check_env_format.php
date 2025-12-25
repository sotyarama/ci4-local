<?php

/**
 * Simple .env formatting checker
 * - Finds files named `.env*` (including `.env.example`) in the repo (excluding vendor/.git/backups)
 * - Flags any unquoted values that contain whitespace (these will break DotEnv parsing)
 *
 * Usage: php scripts/check_env_format.php
 */

$root = realpath(__DIR__ . '/..');
$skipDirs = ['.git', 'vendor', 'backups', 'node_modules', '.idea', '.vscode'];

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$files = [];

foreach ($rii as $file) {
    if ($file->isDir()) {
        continue;
    }

    $path = $file->getPathname();

    // Skip unwanted directories
    foreach ($skipDirs as $sd) {
        if (strpos($path, DIRECTORY_SEPARATOR . $sd . DIRECTORY_SEPARATOR) !== false) {
            continue 2;
        }
    }

    $basename = basename($path);

    // Match files that are .env, .env.example, or start with .env
    if (str_starts_with($basename, '.env')) {
        $files[] = $path;
    }
}

if (empty($files)) {
    echo "No .env files found to check.\n";
    exit(0);
}

$errors = [];

foreach ($files as $file) {
    $lines = @file($file, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        echo "Warning: could not read file: $file\n";
        continue;
    }

    foreach ($lines as $i => $line) {
        $raw  = rtrim($line, "\r\n");
        $trim = trim($raw);

        if ($trim === '' || str_starts_with($trim, '#')) {
            continue;
        }

        if (! str_contains($trim, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $trim, 2);
        $value = trim($value);

        if ($value === '') {
            continue;
        }

        $first = $value[0] ?? '';
        // Quoted values are allowed
        if ($first === '"' || $first === "'") {
            continue;
        }

        // Unquoted values that contain whitespace are invalid for DotEnv
        if (preg_match('/\s/', $value)) {
            $errors[] = ['file' => $file, 'line' => $i + 1, 'content' => $raw];
        }
    }
}

if (! empty($errors)) {
    echo "ENV format check found issues:\n";
    foreach ($errors as $e) {
        echo "- {$e['file']}:{$e['line']}: " . trim($e['content']) . "\n";
    }
    echo "\nPlease wrap values that contain spaces in quotes, for example:\n";
    echo "  email.fromName = \"TEMU RASA CAFE\"\n";
    exit(1);
}

echo "ENV format check passed: no unquoted values with spaces found.\n";
exit(0);
