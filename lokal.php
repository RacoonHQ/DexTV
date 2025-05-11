<?php
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/includes/login.php');
    exit;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

include 'includes/header.php';
include 'includes/loading.php';
include 'includes/chat.php';

renderChannelPage('lokal');

include 'includes/footer.php';
?>
