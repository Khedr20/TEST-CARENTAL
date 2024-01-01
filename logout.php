<?php
require_once 'User.php';

// Assuming $db is already defined or you need to instantiate it here
// For example: $db = new Database();

$user = new User($db);
$user->logout();
?>
