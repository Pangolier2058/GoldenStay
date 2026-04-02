<?php
// header.php

require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/functions.php';

$isLoggedIn = isset($_SESSION['user_id']);
$siteName = getenv('SITE_NAME') ?: 'Golden Stay';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?= $siteName ?> | Гостиничный комплекс</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover fixed;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(244, 247, 252, 0.88);
            z-index: -1;
        }
        
        .custom-header {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 1rem 0;
        }
        
        .logo { display: inline-block; margin-bottom: 0.8rem; }
        .logo-text {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 1px;
            text-decoration: none;
        }
        .logo-text span { color: #f7c948; }
        
        .nav-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .nav-menu { display: flex; gap: 2rem; align-items: center; flex-wrap: wrap; }
        .nav-link-custom {
            color: #f0f0f0 !important;
            transition: 0.2s;
            border-bottom: 2px solid transparent;
            font-size: 1rem;
            text-decoration: none;
            padding: 0.5rem 0;
            white-space: nowrap;
        }
        .nav-link-custom:hover { color: #f7c948 !important; border-bottom-color: #f7c948; }
        
        .booking-link-nav {
            background: #f7c948;
            color: #1e4663 !important;
            padding: 0.4rem 1.2rem !important;
            border-radius: 40px;
            border-bottom: none !important;
            font-weight: 600;
        }
        .booking-link-nav:hover {
            background: #ffd966;
            color: #1e4663 !important;
            transform: scale(1.02);
        }
        
        .login-btn-custom, .profile-btn-custom {
            background: #f7c948;
            color: #1e4663;
            border: none;
            padding: 0.5rem 1.3rem;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }
        .login-btn-custom:hover, .profile-btn-custom:hover {
            background: #ffd966;
            transform: scale(1.02);
        }
        
        .hero-custom {
            background: linear-gradient(105deg, rgba(233, 240, 245, 0.95), rgba(255, 255, 255, 0.95));
            backdrop-filter: blur(2px);
            padding: 3rem 2rem !important;
        }
        .hero-custom h1 { font-size: 2.8rem; }
        .hero-custom p { font-size: 1.2rem; }
        
        /* Карточки корпусов */
        .buildings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .building-card {
            cursor: pointer;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 15px 30px -12px rgba(0,0,0,0.12);
            border: 1px solid #eef2f5;
        }
        .building-card:hover { transform: translateY(-8px); box-shadow: 0 25px 40px -15px rgba(0,0,0,0.2); }
        
        .building-img {
            height: 350px;
            background-size: cover;
            background-position: center;
            position: relative;
            flex-shrink: 0;
        }
        
        .badge-corpus {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #f7c948;
            color: #2c3e4e;
            font-weight: bold;
            padding: 0.6rem 1.3rem;
            border-radius: 40px;
            font-size: 1rem;
            z-index: 2;
        }
        
        .rating-badge {
            position: absolute;
            bottom: 16px;
            left: 20px;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(4px);
            padding: 0.5rem 1.2rem;
            border-radius: 30px;
            color: #f7c948;
            font-size: 1rem;
            font-weight: bold;
            z-index: 2;
        }
        
        .building-info { padding: 1.8rem; display: flex; flex-direction: column; flex: 1; }
        
        .building-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.8rem;
            margin-bottom: 1rem;
        }
        .building-name { font-size: 1.8rem; font-weight: bold; color: #1e4663; margin: 0; }
        .building-distance {
            font-size: 0.85rem;
            background: #eef2fa;
            padding: 0.4rem 1rem;
            border-radius: 30px;
            color: #1e4663;
            white-space: nowrap;
        }
        .building-desc {
            color: #4a627a;
            font-size: 1rem;
            line-height: 1.5;
            margin-bottom: 1.2rem;
        }
        
        .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.7rem;
            margin: 0.8rem 0;
            padding-top: 0.8rem;
            border-top: 1px solid #edf2f7;
        }
        .amenity-tag {
            background: #f1f5f9;
            border-radius: 25px;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: #2c5a6e;
            display: inline-block;
        }
        
        .price-wrapper { margin: 0.8rem 0; }
        .price-badge {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e4663;
            background: #eef3fc;
            padding: 0.5rem 1.2rem;
            border-radius: 25px;
            display: inline-block;
        }
        
        .rooms-preview { display: flex; flex-wrap: wrap; gap: 0.7rem; margin: 0.8rem 0; }
        .room-tag {
            background: #edf2f7;
            padding: 0.4rem 1rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #1e4663;
            display: inline-block;
        }
        
        .card-footer-custom {
            margin-top: auto;
            padding-top: 1.2rem;
            border-top: 1px solid #f0f0f0;
            width: 100%;
        }
        
        .select-btn {
            width: 100%;
            background: #1e4663;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            font-size: 1rem;
        }
        .select-btn:hover { background: #f7c948; color: #1e2a3a; }
        
        /* Карточки номеров */
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
            gap: 2rem;
            margin-top: 1.5rem;
        }
        
        .room-card {
            background: white;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #eef2f5;
        }
        .room-card:hover { transform: translateY(-5px); box-shadow: 0 20px 35px rgba(0,0,0,0.15); }
        
        .room-image {
            width: 100%;
            height: 280px;
            background-size: cover;
            background-position: center;
            position: relative;
            flex-shrink: 0;
        }
        
        .room-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #f7c948;
            color: #1e4663;
            padding: 0.5rem 1.2rem;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: bold;
            z-index: 2;
        }
        
        .room-content { padding: 1.8rem; flex: 1; display: flex; flex-direction: column; background: white; }
        .room-header { margin-bottom: 1.5rem; border-bottom: 3px solid #f7c948; padding-bottom: 1rem; }
        .room-name { font-size: 1.6rem; font-weight: 700; color: #1e4663; margin-bottom: 0.5rem; }
        .room-capacity {
            font-size: 0.9rem;
            color: #f7c948;
            background: #1e2a3a;
            display: inline-block;
            padding: 0.3rem 1rem;
            border-radius: 25px;
            font-weight: 500;
        }
        
        .room-features { margin-bottom: 1.5rem; flex: 1; }
        .feature-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.9rem 0;
            border-bottom: 1px solid #eef2f5;
            gap: 1rem;
        }
        .feature-row:last-child { border-bottom: none; }
        .feature-label {
            font-weight: 700;
            color: #2c5a6e;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            min-width: 110px;
        }
        .feature-value {
            color: #1e4663;
            font-weight: 500;
            font-size: 1rem;
            text-align: right;
            flex: 1;
        }
        
        .room-price-wrapper { text-align: center; margin: 1rem 0 1.2rem; }
        .room-price {
            font-size: 1.4rem;
            font-weight: bold;
            color: #f7c948;
            background: #1e2a3a;
            padding: 0.6rem 1.5rem;
            border-radius: 40px;
            display: inline-block;
        }
        
        .book-btn {
            width: 100%;
            background: #2c5a2e;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: auto;
        }
        .book-btn:hover:not(:disabled) { background: #1f7a1f; transform: scale(1.02); }
        .book-btn:disabled { opacity: 0.5; cursor: not-allowed; background: #95a5a6; }
        
        .custom-footer {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #ccc;
            margin-top: 3rem;
        }
        .contact-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #ccc;
            text-decoration: none;
            transition: 0.2s;
        }
        .contact-link:hover { color: #f7c948; }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            max-width: 420px;
            width: 90%;
            padding: 2rem 1.8rem;
            border-radius: 32px;
            position: relative;
            box-shadow: 0 20px 35px rgba(0,0,0,0.3);
            animation: fadeSlide 0.25s ease;
        }
        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .close-modal {
            position: absolute;
            top: 18px;
            right: 22px;
            font-size: 1.8rem;
            cursor: pointer;
            color: #6c757d;
        }
        
        @media (max-width: 992px) {
            .buildings-grid { grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); }
            .rooms-grid { grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); }
            .hero-custom h1 { font-size: 2.2rem; }
            .nav-wrapper { flex-direction: column; align-items: stretch; }
            .nav-menu { justify-content: center; }
            .profile-btn-custom, .login-btn-custom { text-align: center; justify-content: center; }
        }
        
        @media (max-width: 768px) {
            .buildings-grid { grid-template-columns: 1fr; }
            .rooms-grid { grid-template-columns: 1fr; }
            .building-img { height: 240px; }
            .room-image { height: 220px; }
            .feature-label { min-width: 95px; font-size: 0.75rem; }
            .hero-custom h1 { font-size: 1.8rem; }
            .hero-custom p { font-size: 1rem; }
            .nav-menu { gap: 1rem; }
            .logo { text-align: center; }
        }
    </style>
</head>
<body>

<header class="custom-header">
    <div class="container py-3">
        <div class="logo">
            <a href="index.php" class="logo-text">🏨✨ Golden<span>Stay</span></a>
        </div>
        
        <div class="nav-wrapper">
            <nav class="nav-menu">
                <a href="index.php" class="nav-link-custom">Главная</a>
                <a href="index.php#buildings" class="nav-link-custom">Корпуса</a>
                <a href="search_rooms.php" class="booking-link-nav">Забронировать</a>
                <a href="#" id="navContacts" class="nav-link-custom">Контакты</a>
            </nav>
            
            <div class="auth-buttons">
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php" class="profile-btn-custom">
                        <i class="bi bi-person-circle"></i> Личный кабинет
                    </a>
                <?php else: ?>
                    <button class="login-btn-custom" id="loginBtnHeader">
                        <i class="bi bi-person-circle"></i> Войти
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navContacts = document.getElementById('navContacts');
    const footer = document.getElementById('footer');
    if (navContacts && footer) {
        navContacts.addEventListener('click', function(e) {
            e.preventDefault();
            footer.scrollIntoView({ behavior: 'smooth' });
        });
    }
});
</script>