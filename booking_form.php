<?php
// booking_form.php

session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    echo '<!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Доступ запрещен | Golden Stay</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { background: url(\'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80\') center/cover fixed; position: relative; }
            body::before { content: \'\'; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(244, 247, 252, 0.88); z-index: -1; }
            
            .custom-header { background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); position: sticky; top: 0; z-index: 1000; padding: 1rem 0; }
            .logo { text-align: left; margin-bottom: 1rem; }
            .logo-text { color: white; font-size: 1.5rem; font-weight: bold; text-decoration: none; }
            .logo-text span { color: #f7c948; }
            .nav-wrapper { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
            .nav-menu { display: flex; gap: 2rem; align-items: center; flex-wrap: wrap; }
            .nav-link-custom { color: #f0f0f0 !important; text-decoration: none; padding: 0.5rem 0; transition: 0.2s; border-bottom: 2px solid transparent; }
            .nav-link-custom:hover { color: #f7c948 !important; border-bottom-color: #f7c948; }
            .booking-link-nav { background: #f7c948; color: #1e4663 !important; padding: 0.4rem 1.2rem !important; border-radius: 40px; font-weight: 600; text-decoration: none; }
            .login-btn-custom { background: #f7c948; color: #1e4663; border: none; padding: 0.5rem 1.3rem; border-radius: 40px; cursor: pointer; }
            
            .alert-container { max-width: 500px; margin: 100px auto; text-align: center; background: white; border-radius: 28px; padding: 2rem; box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15); }
            .alert-icon { font-size: 4rem; color: #f7c948; margin-bottom: 1rem; }
            .back-link { display: inline-block; margin-top: 1rem; color: #4a627a; text-decoration: none; }
            
            .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 2000; align-items: center; justify-content: center; }
            .modal-content { background: white; max-width: 420px; width: 90%; padding: 2rem; border-radius: 32px; position: relative; animation: fadeSlide 0.25s ease; }
            @keyframes fadeSlide { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
            .close-modal { position: absolute; top: 15px; right: 20px; font-size: 1.8rem; cursor: pointer; color: #6c757d; }
            
            .custom-footer { background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); color: #ccc; margin-top: 3rem; padding: 2rem 5%; text-align: center; }
            @media (max-width: 768px) { .logo { text-align: center; } .nav-wrapper { flex-direction: column; text-align: center; } .nav-menu { justify-content: center; gap: 1rem; } }
        </style>
    </head>
    <body>
        <header class="custom-header"><div class="container"><div class="logo"><a href="index.php" class="logo-text">🏨✨ Golden<span>Stay</span></a></div>
        <div class="nav-wrapper"><nav class="nav-menu"><a href="index.php" class="nav-link-custom">Главная</a><a href="index.php#buildings" class="nav-link-custom">Корпуса</a><a href="search_rooms.php" class="booking-link-nav">Забронировать</a><a href="#" id="navContacts" class="nav-link-custom">Контакты</a></nav>
        <div class="auth-buttons"><button class="login-btn-custom" onclick="openLoginModal()"><i class="bi bi-person-circle"></i> Войти</button></div></div></div></header>
        
        <div class="container"><div class="alert-container"><div class="alert-icon"><i class="bi bi-shield-lock-fill"></i></div>
        <div class="alert-title">⚠️ Требуется авторизация</div><div class="alert-message">Для бронирования номера необходимо войти в систему.</div>
        <button onclick="openLoginModal()" class="login-btn-custom w-100">🔐 Войти в систему</button><br><a href="index.php" class="back-link">← Вернуться на главную</a></div></div>
        
        <div id="authModal" class="modal"><div class="modal-content"><span class="close-modal" id="closeModalBtn">&times;</span>
        <h2 class="text-center mb-4">🔐 Вход в личный кабинет</h2>
        <form id="loginFormModal" onsubmit="return false;"><input type="text" id="loginUsername" class="form-control rounded-pill py-2 px-3 mb-3" placeholder="ФИО гостя" required>
        <input type="password" id="loginPassword" class="form-control rounded-pill py-2 px-3 mb-3" placeholder="Пароль" required>
        <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 fw-bold">Войти</button>
        <div class="text-center mt-3"><a href="#" class="small">Забыли пароль?</a> | <a href="register.php" class="small">Регистрация</a></div></form></div></div>
        
        <footer class="custom-footer"><p>© 2026 Golden Stay — гостиничный комплекс, 5 корпусов у моря. Все права защищены.</p></footer>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            function openLoginModal() { document.getElementById("authModal").style.display = "flex"; }
            document.getElementById("closeModalBtn")?.addEventListener("click", () => document.getElementById("authModal").style.display = "none");
            window.onclick = (e) => { if (e.target === document.getElementById("authModal")) e.target.style.display = "none"; };
            
            document.getElementById("loginFormModal")?.addEventListener("submit", function(e) {
                e.preventDefault();
                const username = document.getElementById("loginUsername").value.trim();
                const password = document.getElementById("loginPassword").value;
                if (!username || !password) return alert("Введите логин и пароль");
                const btn = this.querySelector("button");
                const original = btn.innerHTML;
                btn.innerHTML = "<i class=\"bi bi-hourglass-split\"></i> Вход...";
                btn.disabled = true;
                fetch("login.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded", "X-Requested-With": "XMLHttpRequest" },
                    body: new URLSearchParams({ username, password, redirect: window.location.href })
                })
                .then(r => r.json())
                .then(d => { btn.innerHTML = original; btn.disabled = false; d.success ? window.location.href = d.redirect : alert(d.message); })
                .catch(() => { btn.innerHTML = original; btn.disabled = false; alert("Ошибка соединения"); });
            });
        </script>
    </body>
    </html>';
    exit;
}

require_once 'models/Visitor.php';
require_once 'functions.php';

$formData = [];
if (isset($_COOKIE['booking_form'])) {
    $formData = json_decode($_COOKIE['booking_form'], true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
    $formData = $_POST;
    setcookie('booking_form', json_encode($formData), time() + 30 * 24 * 60 * 60, '/');
}

$visitorModel = new Visitor();
$visitor = $visitorModel->getById($_SESSION['user_id']);

$room_id = $_GET['room_id'] ?? $formData['room_id'] ?? 0;
$room_name = $_GET['room_name'] ?? $formData['room_name'] ?? '';
$corpus_name = $_GET['corpus_name'] ?? $formData['corpus_name'] ?? '';
$price = $_GET['price'] ?? $formData['price'] ?? '';
$beds = $_GET['beds'] ?? $formData['beds'] ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование номера | Golden Stay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover fixed; position: relative; }
        body::before { content: ''; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(244, 247, 252, 0.88); z-index: -1; }
        
        .booking-container { max-width: 1200px; margin: 2rem auto; }
        .booking-card { background: white; border-radius: 32px; padding: 2.5rem; box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15); border: 1px solid #eef2f5; }
        .booking-header { text-align: center; margin-bottom: 2rem; }
        .booking-header h1 { font-size: 2rem; color: #1e4663; margin-bottom: 0.5rem; }
        .booking-header p { color: #4a627a; font-size: 1rem; }
        
        .selected-room-info { background: #eef3fc; border-radius: 20px; padding: 1.2rem; margin-bottom: 2rem; border-left: 4px solid #f7c948; }
        .selected-room-info p { margin: 0.4rem 0; color: #1e4663; font-size: 1rem; }
        
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.6rem; font-weight: 600; color: #1e4663; font-size: 0.95rem; }
        .form-group input, .form-group select { width: 100%; padding: 1rem 1.2rem; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 1rem; background: #f9fafc; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #f7c948; box-shadow: 0 0 0 3px rgba(247, 201, 72, 0.1); background: white; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        
        .checkbox-group, .radio-group { display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 0.5rem; }
        .checkbox-group label, .radio-group label { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.6rem 1.2rem; background: #f9fafc; border-radius: 40px; border: 1px solid #e2e8f0; transition: 0.2s; }
        .checkbox-group label:hover, .radio-group label:hover { background: #eef3fc; border-color: #f7c948; }
        .checkbox-group input, .radio-group input { width: auto; margin: 0; }
        
        .file-group { margin: 1.5rem 0; }
        .file-input { width: 100%; padding: 1rem 1.2rem; border: 1px solid #e2e8f0; border-radius: 16px; background: #f9fafc; cursor: pointer; }
        .file-name { margin-top: 0.5rem; font-size: 0.85rem; color: #2c5a2e; padding: 0.3rem 0.8rem; background: #e8f5e9; border-radius: 20px; display: inline-block; }
        .file-hint { font-size: 0.75rem; color: #6c757d; margin-top: 0.4rem; }
        
        .btn-submit { width: 100%; background: #1e4663; color: white; border: none; padding: 1.2rem; border-radius: 50px; font-weight: 600; font-size: 1.1rem; cursor: pointer; transition: 0.2s; margin-top: 1rem; }
        .btn-submit:hover { background: #f7c948; color: #1e2a3a; transform: translateY(-2px); }
        
        .back-link { display: inline-flex; align-items: center; gap: 0.5rem; color: #4a627a; text-decoration: none; margin-bottom: 1rem; transition: 0.2s; }
        .back-link:hover { color: #f7c948; }
        .section-title { font-size: 1.3rem; color: #1e4663; margin: 1.5rem 0 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #f7c948; display: inline-block; }
        
        .custom-header { background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); position: sticky; top: 0; z-index: 1000; padding: 1rem 0; }
        .logo { text-align: left; margin-bottom: 1rem; }
        .logo-text { color: white; font-size: 1.5rem; font-weight: bold; text-decoration: none; }
        .logo-text span { color: #f7c948; }
        .nav-wrapper { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .nav-menu { display: flex; gap: 2rem; align-items: center; flex-wrap: wrap; }
        .nav-link-custom { color: #f0f0f0 !important; text-decoration: none; padding: 0.5rem 0; transition: 0.2s; border-bottom: 2px solid transparent; }
        .nav-link-custom:hover { color: #f7c948 !important; border-bottom-color: #f7c948; }
        .booking-link-nav { background: #f7c948; color: #1e4663 !important; padding: 0.4rem 1.2rem !important; border-radius: 40px; font-weight: 600; text-decoration: none; }
        .profile-btn-custom, .login-btn-custom { background: #f7c948; color: #1e4663; border: none; padding: 0.5rem 1.3rem; border-radius: 40px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
        .custom-footer { background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); color: #ccc; margin-top: 3rem; padding: 2rem 5%; text-align: center; }
        
        @media (max-width: 992px) { .booking-container { max-width: 95%; } .form-row { grid-template-columns: 1fr; gap: 0; } }
        @media (max-width: 768px) { .booking-card { padding: 1.5rem; } .booking-header h1 { font-size: 1.6rem; } .checkbox-group label, .radio-group label { padding: 0.4rem 1rem; font-size: 0.85rem; } .btn-submit { padding: 1rem; font-size: 1rem; } .logo { text-align: center; } .nav-wrapper { flex-direction: column; align-items: stretch; text-align: center; } .nav-menu { justify-content: center; gap: 1rem; } }
    </style>
</head>
<body>

<header class="custom-header">
    <div class="container">
        <div class="logo"><a href="index.php" class="logo-text">🏨✨ Golden<span>Stay</span></a></div>
        <div class="nav-wrapper">
            <nav class="nav-menu">
                <a href="index.php" class="nav-link-custom">Главная</a>
                <a href="index.php#buildings" class="nav-link-custom">Корпуса</a>
                <a href="search_rooms.php" class="booking-link-nav">Забронировать</a>
                <a href="#" id="navContacts" class="nav-link-custom">Контакты</a>
            </nav>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="profile-btn-custom"><i class="bi bi-person-circle"></i> Личный кабинет</a>
                <?php else: ?>
                    <button class="login-btn-custom" id="loginBtnHeader"><i class="bi bi-person-circle"></i> Войти</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<main>
    <div class="container booking-container">
        <a href="search_rooms.php" class="back-link"><i class="bi bi-arrow-left"></i> Вернуться к поиску</a>
        
        <div class="booking-card">
            <div class="booking-header">
                <h1>📅 Оформление бронирования</h1>
                <p>Заполните данные для подтверждения</p>
            </div>
            
            <div class="selected-room-info">
                <p><strong>🏠 Выбранный номер:</strong> <?= htmlspecialchars($room_name) ?></p>
                <p><strong>🏢 Корпус:</strong> <?= htmlspecialchars($corpus_name) ?></p>
                <p><strong>💰 Цена:</strong> <?= htmlspecialchars($price) ?></p>
                <p><strong>🛏️ Вместимость:</strong> <?= htmlspecialchars($beds) ?></p>
            </div>
            
            <form action="process_booking.php" method="POST" enctype="multipart/form-data" id="bookingForm">
                <input type="hidden" name="room_id" value="<?= $room_id ?>">
                <input type="hidden" name="room_name" value="<?= htmlspecialchars($room_name) ?>">
                <input type="hidden" name="corpus_name" value="<?= htmlspecialchars($corpus_name) ?>">
                <input type="hidden" name="price" value="<?= htmlspecialchars($price) ?>">
                <input type="hidden" name="beds" value="<?= htmlspecialchars($beds) ?>">
                
                <div class="section-title">👤 Личные данные</div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="fullname">ФИО *</label>
                        <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($formData['fullname'] ?? $visitor['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($formData['email'] ?? $visitor['email'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Телефон *</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($formData['phone'] ?? $visitor['phone'] ?? '') ?>" required>
                </div>
                
                <div class="section-title">📅 Даты проживания</div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="check_in">Дата заезда *</label>
                        <input type="date" id="check_in" name="check_in" value="<?= htmlspecialchars($formData['check_in'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="check_out">Дата выезда *</label>
                        <input type="date" id="check_out" name="check_out" value="<?= htmlspecialchars($formData['check_out'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="guests">Количество гостей *</label>
                    <input type="number" id="guests" name="guests" min="1" max="10" value="<?= htmlspecialchars($formData['guests'] ?? '1') ?>" required>
                </div>
                
                <div class="section-title">🍽️ Питание (дополнительная плата)</div>
                <div class="radio-group">
                    <label><input type="radio" name="meal_plan" value="none" <?= (($formData['meal_plan'] ?? '') == 'none') ? 'checked' : '' ?>> Без питания (0 ₽)</label>
                    <label><input type="radio" name="meal_plan" value="breakfast" <?= (($formData['meal_plan'] ?? '') == 'breakfast') ? 'checked' : '' ?>> Завтрак (шведский стол) — <strong>+800 ₽/день</strong></label>
                    <label><input type="radio" name="meal_plan" value="half_board" <?= (($formData['meal_plan'] ?? '') == 'half_board') ? 'checked' : '' ?>> Полупансион (завтрак + ужин) — <strong>+1500 ₽/день</strong></label>
                    <label><input type="radio" name="meal_plan" value="full_board" <?= (($formData['meal_plan'] ?? '') == 'full_board') ? 'checked' : '' ?>> Полный пансион (завтрак + обед + ужин) — <strong>+2200 ₽/день</strong></label>
                </div>
                <div class="file-hint" style="margin-bottom: 1rem;">💡 Цены указаны за человека в день</div>
                
                <div class="section-title">✨ Дополнительные услуги</div>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="services[]" value="parking" <?= (in_array('parking', ($formData['services'] ?? []))) ? 'checked' : '' ?>> 🚗 Парковка (500 ₽/день)</label>
                    <label><input type="checkbox" name="services[]" value="spa" <?= (in_array('spa', ($formData['services'] ?? []))) ? 'checked' : '' ?>> 💆‍♀️ СПА-центр (1500 ₽/посещение)</label>
                    <label><input type="checkbox" name="services[]" value="transfer" <?= (in_array('transfer', ($formData['services'] ?? []))) ? 'checked' : '' ?>> 🚐 Трансфер от аэропорта (2500 ₽)</label>
                    <label><input type="checkbox" name="services[]" value="excursion" <?= (in_array('excursion', ($formData['services'] ?? []))) ? 'checked' : '' ?>> 🏛️ Экскурсионное обслуживание (3000 ₽)</label>
                </div>
                
                <div class="section-title">🐾 Дополнительные опции</div>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="options[]" value="pets" <?= (in_array('pets', ($formData['options'] ?? []))) ? 'checked' : '' ?>> 🐕 Размещение с питомцем (1000 ₽/день)</label>
                    <label><input type="checkbox" name="options[]" value="baby_cot" <?= (in_array('baby_cot', ($formData['options'] ?? []))) ? 'checked' : '' ?>> 👶 Детская кроватка (бесплатно)</label>
                    <label><input type="checkbox" name="options[]" value="late_checkout" <?= (in_array('late_checkout', ($formData['options'] ?? []))) ? 'checked' : '' ?>> ⏰ Поздний выезд до 18:00 (1500 ₽)</label>
                    <label><input type="checkbox" name="options[]" value="early_checkin" <?= (in_array('early_checkin', ($formData['options'] ?? []))) ? 'checked' : '' ?>> 🌅 Ранний заезд с 8:00 (1000 ₽)</label>
                    <label><input type="checkbox" name="options[]" value="room_service" <?= (in_array('room_service', ($formData['options'] ?? []))) ? 'checked' : '' ?>> 🛎️ Завтрак в номер (300 ₽/день)</label>
                </div>
                
                <div class="file-group">
                    <label for="passport">📎 Загрузить фото паспорта *</label>
                    <input type="file" id="passport" name="passport" accept="image/*,.pdf" class="file-input" required onchange="updateFileName(this)">
                    <div id="fileNameDisplay" class="file-name" style="display: none;"></div>
                    <div class="file-hint">Допустимые форматы: JPG, PNG, PDF. Максимальный размер: 10 МБ</div>
                </div>
                
                <button type="submit" name="submit_booking" class="btn-submit">📝 Подтвердить бронирование</button>
            </form>
        </div>
    </div>
</main>

<footer class="custom-footer">
    <p>© 2026 Golden Stay — гостиничный комплекс, 5 корпусов у моря. Все права защищены.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const today = new Date().toISOString().split('T')[0];
const checkIn = document.getElementById('check_in');
const checkOut = document.getElementById('check_out');
if (checkIn) checkIn.min = today;
if (checkOut) checkOut.min = today;
if (checkIn) checkIn.addEventListener('change', () => { if (checkOut) checkOut.min = checkIn.value; });

function updateFileName(input) {
    const display = document.getElementById('fileNameDisplay');
    if (input.files?.length) {
        display.textContent = '📎 Выбран файл: ' + input.files[0].name;
        display.style.display = 'inline-block';
    } else {
        display.style.display = 'none';
    }
}

document.getElementById('navContacts')?.addEventListener('click', (e) => {
    e.preventDefault();
    document.querySelector('.custom-footer')?.scrollIntoView({ behavior: 'smooth' });
});
</script>
</body>
</html>