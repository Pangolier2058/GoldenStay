<?php
// process_booking.php

session_start();
require_once 'models/Booking.php';
require_once 'models/Visitor.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: search_rooms.php');
    exit;
}

// Получаем данные
$roomId = $_POST['room_id'] ?? 0;
$roomName = $_POST['room_name'] ?? '';
$corpusName = $_POST['corpus_name'] ?? '';
$price = $_POST['price'] ?? '';
$beds = $_POST['beds'] ?? '';
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$checkIn = $_POST['check_in'] ?? '';
$checkOut = $_POST['check_out'] ?? '';
$guests = $_POST['guests'] ?? 1;
$mealPlan = $_POST['meal_plan'] ?? 'none';
$services = $_POST['services'] ?? [];
$options = $_POST['options'] ?? [];

// Валидация
if (!$roomId || !$checkIn || !$checkOut || !$fullname || !$email || !$phone) {
    $_SESSION['booking_error'] = 'Заполните все обязательные поля';
    header('Location: booking_form.php?room_id=' . $roomId);
    exit;
}

if (strtotime($checkIn) >= strtotime($checkOut)) {
    $_SESSION['booking_error'] = 'Дата выезда должна быть позже даты заезда';
    header('Location: booking_form.php?room_id=' . $roomId);
    exit;
}

if (strtotime($checkIn) < strtotime(date('Y-m-d'))) {
    $_SESSION['booking_error'] = 'Дата заезда не может быть в прошлом';
    header('Location: booking_form.php?room_id=' . $roomId);
    exit;
}

// Сохраняем данные в cookie
$bookingData = [
    'fullname' => $fullname,
    'email' => $email,
    'phone' => $phone,
    'check_in' => $checkIn,
    'check_out' => $checkOut,
    'guests' => $guests,
    'meal_plan' => $mealPlan,
    'services' => $services,
    'options' => $options,
    'room_id' => $roomId,
    'room_name' => $roomName,
    'corpus_name' => $corpusName,
    'price' => $price,
    'beds' => $beds
];
setcookie('booking_form', json_encode($bookingData), time() + 30 * 24 * 60 * 60, '/');

// Обработка загрузки файла паспорта (сохраняем в обычную папку, без БД)
$uploadDir = __DIR__ . '/uploads/documents/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$passportFile = '';
$uploadError = '';

if (isset($_FILES['passport']) && $_FILES['passport']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['passport'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'pdf'];
    
    if (in_array($ext, $allowedExts)) {
        $filename = time() . '_' . uniqid() . '.' . $ext;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $passportFile = 'uploads/documents/' . $filename;
        } else {
            $uploadError = 'Ошибка при загрузке файла';
        }
    } else {
        $uploadError = 'Недопустимый формат файла. Разрешены: JPG, PNG, PDF';
    }
} else {
    $uploadError = 'Файл паспорта не загружен';
}

// Обновляем данные пользователя
$visitorModel = new Visitor();
$visitorModel->update($_SESSION['user_id'], [
    'name' => $fullname,
    'email' => $email,
    'phone' => $phone
]);

// Создаем бронирование
$bookingModel = new Booking();
$result = $bookingModel->create($roomId, $_SESSION['user_id'], $checkIn, $checkOut, $guests);

// Сохраняем результат в сессию
$_SESSION['booking_result'] = [
    'success' => $result['success'],
    'message' => $result['success'] ? 'Бронирование успешно оформлено!' : $result['message'],
    'data' => [
        'fullname' => $fullname,
        'email' => $email,
        'phone' => $phone,
        'room_name' => $roomName,
        'corpus_name' => $corpusName,
        'price' => $price,
        'beds' => $beds,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'guests' => $guests,
        'meal_plan' => $mealPlan,
        'services' => $services,
        'options' => $options,
        'passport_file' => $passportFile,
        'upload_error' => $uploadError
    ]
];

header('Location: booking_result.php');
exit;
?>