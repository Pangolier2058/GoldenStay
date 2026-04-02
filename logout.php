<?php
// logout.php

session_start();
$username = $_SESSION['username'] ?? 'Гость';
session_destroy();
session_start();
$_SESSION['logout_message'] = 'До свидания, ' . htmlspecialchars($username) . '!';
header('Location: index.php');
exit;