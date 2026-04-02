<?php
// login.php

session_start();
require_once 'models/Visitor.php';

$visitorModel = new Visitor();
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? 'index.php';
    
    $response = ['success' => false, 'message' => ''];
    
    if (empty($username) || empty($password)) {
        $response['message'] = 'Введите логин и пароль';
    } else {
        $visitor = $visitorModel->login($username, $password);
        
        if ($visitor) {
            $_SESSION['user_id'] = $visitor['id'];
            $_SESSION['username'] = $visitor['name'];
            $_SESSION['user_email'] = $visitor['email'] ?? '';
            $_SESSION['user_phone'] = $visitor['phone'] ?? '';
            $_SESSION['login_message'] = 'Добро пожаловать, ' . htmlspecialchars($visitor['name']) . '!';
            
            $response['success'] = true;
            $response['redirect'] = $redirect;
        } else {
            $response['message'] = 'Неверный логин или пароль';
        }
    }
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        if ($response['success']) {
            header('Location: ' . $response['redirect']);
        } else {
            $_SESSION['login_error'] = $response['message'];
            header('Location: ' . $redirect);
        }
    }
    exit;
}

header('Location: index.php');