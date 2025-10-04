<?php
// PHPUnit bootstrap for spPortalSystem tests
// Sets up constants and includes required classes.

// Ensure document root and server vars exist for config.inc.php defaults
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = getcwd();
}
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

// Load project config and core classes
require_once __DIR__ . '/../config.inc.php';
require_once __DIR__ . '/../includes/CHelper.php';
require_once __DIR__ . '/../includes/CLog.php';
require_once __DIR__ . '/../includes/CDatabase.php';

// Silence headers and other outputs during tests
ob_start();
register_shutdown_function(function(){
    if (ob_get_level() > 0) { ob_end_clean(); }
});
