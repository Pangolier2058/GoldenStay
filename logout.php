<?php
// logout.php

session_start();
$username = $_SESSION['username'] ?? 'Гость';

// Очищаем cookie с данными формы бронирования
if (isset($_COOKIE['booking_form'])) {
    setcookie('booking_form', '', time() - 3600, '/');
}

session_destroy();
session_start();
$_SESSION['logout_message'] = 'До свидания, ' . htmlspecialchars($username) . '!';
header('Location: index.php');
exit;