<?php
/**
 * CLI demo seed on server (optional; wp-admin → Demo is easier).
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Run from CLI only, or use wp-admin → Demo.\n");
    exit(1);
}

$force = in_array('--force', $argv, true);

$wp_load = getenv('WP_LOAD_PATH');
if (!$wp_load) {
    $wp_load = dirname(__DIR__, 4) . '/wp-load.php';
}

if (!is_file($wp_load)) {
    fwrite(STDERR, "wp-load.php not found. Set WP_LOAD_PATH.\n");
    exit(1);
}

require $wp_load;

$printer = static function ($line) {
    echo $line . PHP_EOL;
};

try {
    silkscreen_import_demo_data(array('force' => $force, 'log' => $printer));
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}