<?php
// Minimal Composer-like autoloader stub used by PhpStorm PHPUnit runner.
// Route to tests/bootstrap.php so environment and classes are loaded.
$bootstrap = dirname(__DIR__) . '/tests/bootstrap.php';
if (file_exists($bootstrap)) {
    require_once $bootstrap;
} else {
    fwrite(STDERR, "Bootstrap file not found: $bootstrap\n");
}
