<?php
// Fallback autoloader for PhpStorm PHPUnit runner.
// This delegates to our test bootstrap so core classes and constants are available.
$bootstrap = __DIR__ . '/tests/bootstrap.php';
if (file_exists($bootstrap)) {
    require_once $bootstrap;
} else {
    fwrite(STDERR, "Bootstrap file not found: $bootstrap\n");
}
