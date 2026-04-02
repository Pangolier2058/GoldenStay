<?php
// booking_result.php

session_start();
require_once 'functions.php';

if (!isset($_SESSION['booking_result'])) {
    header('Location: index.php');
    exit;
}

$result = $_SESSION['booking_result'];
unset($_SESSION['booking_result']);
$data = $result['data'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат бронирования | Golden Stay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover fixed; position: relative; }
        body::before { content: ''; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(244, 247, 252, 0.88); z-index: -1; }
        
        .result-container { max-width: 1200px; margin: 2rem auto; }
        .result-card { background: white; border-radius: 32px; padding: 2.5rem; box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15); border: 1px solid #eef2f5; }
        .result-header { text-align: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 2px solid #eef2f5; }
        .result-icon { font-size: 5rem; margin-bottom: 1rem; }
        .result-icon.success { color: #2c5a2e; }
        .result-icon.error { color: #dc3545; }
        .result-title { font-size: 2rem; color: #1e4663; margin-bottom: 0.5rem; }
        .result-message { color: #4a627a; font-size: 1rem; }
        .info-alert { background: #e8f4fd; border: 1px solid #b8e0fc; border-radius: 16px; padding: 1rem; margin-top: 1rem; }
        
        .data-section { margin: 1.5rem 0; padding: 1.5rem; background: #f9fafc; border-radius: 24px; border: 1px solid #eef2f5; }
        .data-section h3 { color: #1e4663; margin-bottom: 1rem; font-size: 1.3rem; border-left: 4px solid #f7c948; padding-left: 0.8rem; }
        .data-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .data-item { display: flex; justify-content: space-between; align-items: center; padding: 0.8rem 0; border-bottom: 1px solid #e2e8f0; }
        .data-item.full-width { grid-column: span 2; }
        .data-label { font-weight: 600; color: #4a627a; font-size: 0.95rem; }
        .data-value { color: #1e4663; font-weight: 500; text-align: right; font-size: 0.95rem; }
        
        .services-list, .options-list { display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: flex-end; }
        .service-tag, .option-tag { background: #eef3fc; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; color: #1e4663; }
        
        .uploaded-image { margin-top: 1rem; text-align: center; }
        .uploaded-image img { max-width: 100%; max-height: 250px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        
        .error-message { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 12px; margin-bottom: 1rem; border-left: 4px solid #dc3545; }
        
        .btn-actions { display: flex; gap: 1rem; justify-content: center; margin-top: 2rem; flex-wrap: wrap; }
        .btn-primary-custom, .btn-secondary-custom { padding: 0.8rem 1.8rem; border-radius: 40px; font-weight: 600; text-decoration: none; display: inline-block; transition: 0.2s; font-size: 0.95rem; }
        .btn-primary-custom { background: #1e4663; color: white; }
        .btn-primary-custom:hover { background: #f7c948; color: #1e2a3a; }
        .btn-secondary-custom { background: #6c757d; color: white; }
        
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
        
        @media (max-width: 992px) { .result-container { max-width: 95%; } .data-grid { grid-template-columns: 1fr; } .data-item.full-width { grid-column: span 1; } .btn-actions { flex-direction: column; align-items: center; } .btn-primary-custom, .btn-secondary-custom { width: 100%; text-align: center; } }
        @media (max-width: 768px) { .result-card { padding: 1.5rem; } .result-title { font-size: 1.5rem; } .data-item { flex-direction: column; align-items: flex-start; gap: 0.3rem; } .data-value { text-align: left; } .logo { text-align: center; } .nav-wrapper { flex-direction: column; text-align: center; } .nav-menu { justify-content: center; gap: 1rem; } }
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
    <div class="container result-container">
        <div class="result-card">
            <div class="result-header">
                <div class="result-icon <?= $result['success'] ? 'success' : 'error' ?>">
                    <?php if ($result['success']): ?>
                        <i class="bi bi-check-circle-fill"></i>
                    <?php else: ?>
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    <?php endif; ?>
                </div>
                <h1 class="result-title"><?= $result['success'] ? '✅ Бронирование оформлено!' : '❌ Ошибка бронирования' ?></h1>
                <p class="result-message"><?= htmlspecialchars($result['message']) ?></p>
                <?php if ($result['success']): ?>
                    <div class="info-alert">
                        <i class="bi bi-info-circle-fill" style="color: #0c63e4;"></i>
                        <strong style="color: #0c63e4;">Ваша заявка принята!</strong><br>
                        <span style="color: #2c5a6e;">После рассмотрения заявки наш менеджер свяжется с вами для подтверждения бронирования.</span>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($data['upload_error']): ?>
                <div class="error-message"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($data['upload_error']) ?></div>
            <?php endif; ?>
            
            <!-- Информация о бронировании -->
            <div class="data-section">
                <h3>📋 Информация о бронировании</h3>
                <div class="data-grid">
                    <div class="data-item"><span class="data-label">🏠 Номер</span><span class="data-value"><?= htmlspecialchars($data['room_name']) ?></span></div>
                    <div class="data-item"><span class="data-label">🏢 Корпус</span><span class="data-value"><?= htmlspecialchars($data['corpus_name']) ?></span></div>
                    <div class="data-item"><span class="data-label">📅 Дата заезда</span><span class="data-value"><?= date('d.m.Y', strtotime($data['check_in'])) ?></span></div>
                    <div class="data-item"><span class="data-label">📅 Дата выезда</span><span class="data-value"><?= date('d.m.Y', strtotime($data['check_out'])) ?></span></div>
                    <div class="data-item"><span class="data-label">👥 Количество гостей</span><span class="data-value"><?= htmlspecialchars($data['guests']) ?></span></div>
                    <div class="data-item"><span class="data-label">💰 Цена за ночь</span><span class="data-value"><?= htmlspecialchars($data['price']) ?></span></div>
                </div>
            </div>
            
            <!-- Данные гостя -->
            <div class="data-section">
                <h3>👤 Данные гостя</h3>
                <div class="data-grid">
                    <div class="data-item full-width"><span class="data-label">ФИО</span><span class="data-value"><?= htmlspecialchars($data['fullname']) ?></span></div>
                    <div class="data-item"><span class="data-label">📧 Email</span><span class="data-value"><?= htmlspecialchars($data['email']) ?></span></div>
                    <div class="data-item"><span class="data-label">📞 Телефон</span><span class="data-value"><?= htmlspecialchars($data['phone']) ?></span></div>
                </div>
            </div>
            
            <!-- Питание -->
            <div class="data-section">
                <h3>🍽️ Питание</h3>
                <div class="data-grid">
                    <div class="data-item"><span class="data-label">Выбранный вариант</span><span class="data-value"><?= getMealPlanText($data['meal_plan']) ?></span></div>
                </div>
            </div>
            
            <?php if (!empty($data['services'])): ?>
            <div class="data-section">
                <h3>✨ Дополнительные услуги</h3>
                <div class="data-grid">
                    <div class="data-item full-width">
                        <span class="data-label">Выбранные услуги</span>
                        <div class="services-list">
                            <?php foreach ($data['services'] as $service): ?>
                                <span class="service-tag"><?= getServiceName($service) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($data['options'])): ?>
            <div class="data-section">
                <h3>🐾 Дополнительные опции</h3>
                <div class="data-grid">
                    <div class="data-item full-width">
                        <span class="data-label">Выбранные опции</span>
                        <div class="options-list">
                            <?php foreach ($data['options'] as $option): ?>
                                <span class="option-tag"><?= getOptionName($option) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Загруженный документ -->
            <?php if ($data['passport_file'] && file_exists($data['passport_file'])): ?>
            <div class="data-section">
                <h3>📎 Загруженный документ</h3>
                <div class="uploaded-image">
                    <?php $ext = strtolower(pathinfo($data['passport_file'], PATHINFO_EXTENSION)); ?>
                    <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])): ?>
                        <img src="<?= htmlspecialchars($data['passport_file']) ?>" alt="Паспорт">
                    <?php else: ?>
                        <p><i class="bi bi-file-pdf-fill" style="font-size: 2rem;"></i></p>
                        <p><a href="<?= htmlspecialchars($data['passport_file']) ?>" target="_blank" class="btn-secondary-custom" style="display: inline-block; padding: 0.5rem 1rem;">📄 Скачать файл</a></p>
                    <?php endif; ?>
                    <p class="text-success mt-2">✓ Файл успешно загружен</p>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="btn-actions">
                <a href="index.php" class="btn-primary-custom">🏠 На главную</a>
                <a href="profile.php" class="btn-secondary-custom">📋 Мои бронирования</a>
                <a href="search_rooms.php" class="btn-secondary-custom">📅 Новое бронирование</a>
            </div>
        </div>
    </div>
</main>

<footer class="custom-footer" id="footer">
    <p>© 2026 Golden Stay — гостиничный комплекс, 5 корпусов у моря. Все права защищены.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('navContacts')?.addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('footer')?.scrollIntoView({ behavior: 'smooth' });
});
</script>
</body>
</html>