<?php
/**
 * Pre-launch checker. Run:  php golive_check.php
 * Do NOT upload this file to the live server.
 */
if (PHP_SAPI !== 'cli') {
    exit('Run this from the terminal.');
}

$cfg    = @file_get_contents(__DIR__ . '/config/config.php') ?: '';
$issues = [];
$ok     = [];

function constant_value(string $cfg, string $name): ?string
{
    return preg_match("/define\('" . $name . "',\s*(?:'([^']*)'|(true|false))\)/", $cfg, $m)
        ? ($m[1] !== '' ? $m[1] : ($m[2] ?? '')) : null;
}

$debug = constant_value($cfg, 'DEBUG_MODE');
$base  = constant_value($cfg, 'BASE_URL');
$user  = constant_value($cfg, 'DB_USER');
$pass  = constant_value($cfg, 'DB_PASS');

($debug === 'false') ? $ok[] = 'DEBUG_MODE is false'
                     : $issues[] = "DEBUG_MODE must be false on the live site (now: $debug)";

(stripos((string) $base, 'localhost') === false && str_starts_with((string) $base, 'https://'))
    ? $ok[] = "BASE_URL is $base"
    : $issues[] = "BASE_URL should be your live https address, no trailing slash (now: $base)";

($user === 'root') ? $issues[] = 'DB_USER is still "root". Use the cPanel database user.' : $ok[] = 'DB_USER is not root';
($pass === '')     ? $issues[] = 'DB_PASS is empty. Use the cPanel database password.'      : $ok[] = 'DB_PASS is set';

$leftovers = array_merge(glob(__DIR__ . '/build_batch*.php') ?: [], [
    __DIR__ . '/setup_admin.php', __DIR__ . '/test_connection.php', __DIR__ . '/database.sql',
]);
foreach ($leftovers as $f) {
    if (file_exists($f)) {
        $issues[] = 'Delete before uploading: ' . basename($f);
    }
}
if (!is_dir(__DIR__ . '/uploads')) {
    $issues[] = 'uploads/ folder is missing';
}
if (!file_exists(__DIR__ . '/assets/images/hero.jpg')) {
    $issues[] = 'Optional: add assets/images/hero.jpg for the home page banner photo';
}

echo "\nGO-LIVE CHECK\n=============\n";
foreach ($ok as $line)     echo "  [OK]    $line\n";
foreach ($issues as $line) echo "  [FIX]   $line\n";
echo $issues ? "\nFix the items above, then run again.\n" : "\nAll clear. Ready to upload.\n";