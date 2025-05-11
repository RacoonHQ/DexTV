<?php
// Site configuration
define('SITE_URL', 'http://localhost/DexTV');
define('SITE_NAME', 'DexTV');
define('SITE_ROOT', dirname(__DIR__));

// Asset paths
define('ASSETS_URL', SITE_URL . '/assets');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');
define('IMAGES_URL', ASSETS_URL . '/images');
define('ICONS_URL', ASSETS_URL . '/icons');

// Start session
session_start();
?>
