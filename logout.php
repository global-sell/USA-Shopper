<?php
require_once 'config/config.php';

// Destroy session and logout
session_unset();
session_destroy();

// Redirect to home page
redirect(SITE_URL . '/login.php');
?>
