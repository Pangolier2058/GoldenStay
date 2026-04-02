<?php
session_start();
require_once 'models/Booking.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Не авторизован']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = $_POST['booking_id'] ?? 0;
    
    if (!$bookingId) {
        echo json_encode(['success' => false, 'message' => 'Не указан ID бронирования']);
        exit;
    }
    
    $bookingModel = new Booking();
    $result = $bookingModel->cancel($bookingId, $_SESSION['user_id']);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Бронирование отменено']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при отмене бронирования']);
    }
    exit;
}
?>