<?php
/**
 * CLI demo seed (local dev).
 *
 *   php seed/import-demo.php
 *   php seed/import-demo.php --force
 *
 * Production: wp-admin → Demo → Import demo data.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Run from CLI only, or use wp-admin → Demo.\n");
    exit(1);
}

$force = in_array('--force', $argv, true);

$theme_dir = dirname(__DIR__);
if (!is_file($theme_dir . '/style.css')) {
    $theme_dir = $theme_dir . '/wordpress';
}

$wp_load = getenv('WP_LOAD_PATH');
if (!$wp_load) {
    $from_theme = dirname($theme_dir, 3) . '/wp-load.php';
    $local_default = '/Users/antonio/Local Sites/localpress/app/public/wp-load.php';
    if (is_file($from_theme)) {
        $wp_load = $from_theme;
    } elseif (is_file($local_default)) {
        $wp_load = $local_default;
    } else {
        $wp_load = $from_theme;
    }
}

if (!is_file($wp_load)) {
    fwrite(STDERR, "wp-load.php not found at {$wp_load}. Set WP_LOAD_PATH.\n");
    exit(1);
}

require $wp_load;

if (!defined('SILKSCREEN_PROJECT_POST_TYPE')) {
    fwrite(STDERR, "Activate the Silkscreen 2026 theme first.\n");
    exit(1);
}

$printer = static function ($line) {
    echo $line . PHP_EOL;
};

try {
    $result = silkscreen_import_demo_data(array(
        'force' => $force,
        'log' => $printer,
    ));
    if (!empty($result['skipped'])) {
        exit(0);
    }
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}