<?php
require_once 'includes/config.php';

// Destroy all sessions
session_destroy();

// Redirect to homepage
header('Location: index.php');
exit();
?>