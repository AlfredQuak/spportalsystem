<?php
// PhpStorm PHPUnit helper compatibility autoloader
// When PhpStorm runs PHPUnit with --no-configuration, it still expects
// an autoloader file. We delegate to our test bootstrap here.
$bootstrap = __DIR__ . '/bootstrap.php';
if (file_exists($bootstrap)) {
    require_once $bootstrap;
} else {
    fwrite(STDERR, "Bootstrap file not found: $bootstrap\n");
}
