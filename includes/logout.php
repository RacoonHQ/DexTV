<?php
require_once 'config.php';
session_start();
session_destroy();
header('Location: ' . SITE_URL . '/index.php');
exit;
