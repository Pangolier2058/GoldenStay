<?php
session_start();
require_once 'models/Building.php';
require_once 'models/Booking.php';

// Функция для склонения слов
function getPluralForm($number, $one, $two, $five) {
    $n = abs($number);
    $n %= 100;
    if ($n >= 5 && $n <= 20) {
        return $five;
    }
    $n %= 10;
    if ($n === 1) {
        return $one;
    }
    if ($n >= 2 && $n <= 4) {
        return $two;
    }
    return $five;
}

// Функция для получения описания номера в зависимости от корпуса и вместимости
function getRoomDescription($buildingId, $numPlace, $roomNumber) {
    $descriptions = [
        1 => [ // Северный корпус - природа, тишина
            1 => "Уютный одноместный номер с видом на сосновый парк. Идеален для спокойного отдыха и работы в тишине.",
            2 => "Просторный номер с двумя раздельными кроватями. Из окна открывается живописный вид на парковую зону.",
            3 => "Семейный номер с дополнительным спальным местом. Есть детская кроватка и игровая зона.",
            4 => "Большой номер для компании. Две спальни, гостиная зона, балкон с видом на парк."
        ],
        2 => [ // Южный корпус - бассейн, СПА, солнце
            1 => "Светлый номер с видом на бассейн. В номере: кондиционер, халаты и тапочки.",
            2 => "Комфортабельный номер с балконом и видом на бассейн. Входит бесплатный доступ в СПА.",
            3 => "Просторный номер с террасой и шезлонгами. Идеален для отдыха у бассейна.",
            4 => "Люкс с видом на бассейн и горы. В номере: джакузи, мини-бар, приватная терраса."
        ],
        3 => [ // Западный корпус - море, закаты, романтика
            1 => "Романтический номер с видом на море. В номере: винный бар, романтическое освещение.",
            2 => "Номер с панорамным окном во всю стену. Наслаждайтесь закатами не выходя из кровати.",
            3 => "Просторный номер с балконом и шезлонгами. Идеален для пар и медового месяца.",
            4 => "Президентский люкс с джакузи у окна и видом на море. Кровать King-size, частный кинотеатр."
        ],
        4 => [ // Восточный корпус - семейный, дети, уют
            1 => "Уютный номер для одного. Есть рабочая зона и доступ в коворкинг.",
            2 => "Семейный номер с детской кроваткой. В номере: игрушки, мультфильмы по ТВ.",
            3 => "Двухкомнатный номер с кухонным уголком. Идеален для семьи с двумя детьми.",
            4 => "Двухуровневые апартаменты с детской игровой зоной и консолью."
        ],
        5 => [ // Центральный корпус - премиум, люкс, статус
            1 => "Дизайнерский номер в центре комплекса. В номере: мини-бар, кофемашина.",
            2 => "Просторный номер с гостиной зоной. Входит консьерж-сервис и шампанское.",
            3 => "Люкс с панорамным видом на город. Личный консьерж, доступ в лаунж-бар.",
            4 => "Пентхаус с террасой 360°. Личный повар, приватный лифт, VIP-обслуживание."
        ]
    ];
    
    // Если описание для конкретного корпуса и вместимости не найдено, берем общее
    if (isset($descriptions[$buildingId][$numPlace])) {
        return $descriptions[$buildingId][$numPlace];
    }
    
    // Общие описания по умолчанию
    $defaultDescriptions = [
        1 => "Уютный номер с современной мебелью. В номере: удобная кровать, кондиционер, телевизор, Wi-Fi.",
        2 => "Просторный номер с двумя кроватями. Идеален для пары или командированных. Есть рабочая зона.",
        3 => "Семейный номер с дополнительным спальным местом. Подходит для отдыха с детьми.",
        4 => "Большой номер для компании. Две спальни, гостиная, полностью оборудованная кухня."
    ];
    
    $key = min($numPlace, 4);
    return $defaultDescriptions[$key] ?? $defaultDescriptions[1];
}

$buildingModel = new Building();
$bookingModel = new Booking();
$buildings = $buildingModel->getAll();

// Получаем параметры поиска
$searchData = [
    'check_in' => $_GET['check_in'] ?? '',
    'check_out' => $_GET['check_out'] ?? '',
    'guests' => $_GET['guests'] ?? 1,
    'building_id' => $_GET['building_id'] ?? ''
];

// Если есть даты, ищем свободные номера
$availableRooms = [];
if (!empty($searchData['check_in']) && !empty($searchData['check_out'])) {
    foreach ($buildings as $building) {
        // Если выбран конкретный корпус - пропускаем другие
        if (!empty($searchData['building_id']) && $building['id'] != $searchData['building_id']) {
            continue;
        }
        
        $rooms = $buildingModel->getRooms($building['id']);
        foreach ($rooms as $room) {
            if ($room['num_place'] >= $searchData['guests']) {
                if ($bookingModel->isRoomAvailable($room['id'], $searchData['check_in'], $searchData['check_out'])) {
                    $availableRooms[] = [
                        'id' => $room['id'],
                        'room_number' => $room['room_number'],
                        'num_place' => $room['num_place'],
                        'price' => $room['price'],
                        'building_name' => $building['house_name'],
                        'building_id' => $building['id'],
                        'image' => $room['image'],
                        'description' => getRoomDescription($building['id'], $room['num_place'], $room['room_number'])
                    ];
                }
            }
        }
    }
    
    usort($availableRooms, function($a, $b) {
        return $a['price'] - $b['price'];
    });
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Поиск свободных номеров | Golden Stay</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .search-container {
            max-width: 1400px;
            margin: 2rem auto;
        }
        
        .search-card {
            background: white;
            border-radius: 32px;
            padding: 2rem;
            box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15);
            border: 1px solid #eef2f5;
            margin-bottom: 2rem;
        }
        
        .search-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            align-items: end;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #1e4663;
            font-size: 0.9rem;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            font-size: 1rem;
            background: #f9fafc;
        }
        
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #f7c948;
            box-shadow: 0 0 0 3px rgba(247, 201, 72, 0.1);
        }
        
        .btn-search {
            background: #1e4663;
            color: white;
            border: none;
            padding: 0.9rem 1.5rem;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            font-size: 1rem;
        }
        
        .btn-search:hover {
            background: #f7c948;
            color: #1e2a3a;
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .results-count {
            color: #4a627a;
            font-size: 0.95rem;
        }
        
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 2rem;
        }
        
        .room-card-search {
            background: white;
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid #eef2f5;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .room-card-search:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 35px -12px rgba(0,0,0,0.2);
        }
        
        .room-image {
            height: 220px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .room-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #f7c948;
            color: #1e4663;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        .room-content {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .room-header {
            margin-bottom: 1rem;
            border-bottom: 2px solid #f7c948;
            padding-bottom: 0.8rem;
        }
        
        .room-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1e4663;
            margin-bottom: 0.3rem;
        }
        
        .room-building {
            font-size: 0.85rem;
            color: #f7c948;
            background: #1e2a3a;
            display: inline-block;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
        }
        
        .room-description {
            color: #4a627a;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            flex: 1;
        }
        
        .room-features {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        
        .feature-tag {
            background: #f1f5f9;
            border-radius: 20px;
            padding: 0.3rem 0.8rem;
            font-size: 0.75rem;
            color: #2c5a6e;
        }
        
        .room-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
            padding: 0.5rem 0;
            border-top: 1px solid #eef2f5;
            border-bottom: 1px solid #eef2f5;
        }
        
        .room-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: #f7c948;
            background: #1e2a3a;
            display: inline-block;
            padding: 0.3rem 1rem;
            border-radius: 30px;
        }
        
        .btn-book {
            width: 100%;
            background: #2c5a2e;
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 40px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 1rem;
        }
        
        .btn-book:hover {
            background: #1f7a1f;
            transform: scale(1.02);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 24px;
            border: 1px solid #eef2f5;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            color: #1e4663;
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: #4a627a;
            margin-bottom: 1.5rem;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a627a;
            text-decoration: none;
            margin-bottom: 1rem;
            transition: 0.2s;
        }
        
        .back-link:hover {
            color: #f7c948;
        }
        
        .login-prompt-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 2001;
            align-items: center;
            justify-content: center;
        }
        
        .login-prompt-content {
            background: white;
            max-width: 400px;
            width: 90%;
            padding: 2rem;
            border-radius: 28px;
            text-align: center;
            box-shadow: 0 20px 35px rgba(0,0,0,0.3);
            animation: fadeSlide 0.25s ease;
        }
        
        .login-prompt-icon {
            font-size: 4rem;
            color: #f7c948;
            margin-bottom: 1rem;
        }
        
        .login-prompt-title {
            font-size: 1.5rem;
            color: #1e4663;
            margin-bottom: 0.5rem;
        }
        
        .login-prompt-message {
            color: #4a627a;
            margin-bottom: 1.5rem;
        }
        
        .login-prompt-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        .btn-login-prompt {
            background: #1e4663;
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }
        
        .btn-login-prompt:hover {
            background: #f7c948;
            color: #1e2a3a;
        }
        
        .btn-cancel-prompt {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }
        
        .btn-cancel-prompt:hover {
            background: #5a6268;
        }
        
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
        
        .modal-content h2 {
            text-align: center;
            margin-bottom: 1.8rem;
            color: #1e4663;
        }
        
        .modal-content input {
            width: 100%;
            padding: 12px 16px;
            margin: 8px 0 16px;
            border: 1px solid #ccc;
            border-radius: 48px;
            font-size: 1rem;
        }
        
        .modal-content button[type="submit"] {
            width: 100%;
            background: #f7c948;
            border: none;
            padding: 12px;
            border-radius: 48px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
        }
        
        .modal-footer {
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.85rem;
        }
        
        @media (max-width: 768px) {
            .search-form {
                grid-template-columns: 1fr;
            }
            .rooms-grid {
                grid-template-columns: 1fr;
            }
            .room-image {
                height: 180px;
            }
            .room-number {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <div class="container search-container">
        <a href="index.php" class="back-link">
            <i class="bi bi-arrow-left"></i> Вернуться на главную
        </a>
        
        <div class="search-card">
            <h2 style="color: #1e4663; margin-bottom: 1.5rem;">🔍 Поиск свободных номеров</h2>
            
            <form method="GET" action="search_rooms.php" class="search-form">
                <div class="form-group">
                    <label for="check_in">Дата заезда *</label>
                    <input type="date" id="check_in" name="check_in" value="<?= htmlspecialchars($searchData['check_in']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="check_out">Дата выезда *</label>
                    <input type="date" id="check_out" name="check_out" value="<?= htmlspecialchars($searchData['check_out']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="guests">Количество гостей</label>
                    <input type="number" id="guests" name="guests" min="1" max="10" value="<?= htmlspecialchars($searchData['guests']) ?>">
                </div>
                <div class="form-group">
                    <label for="building_id">Корпус</label>
                    <select id="building_id" name="building_id">
                        <option value="">Все корпуса</option>
                        <?php foreach ($buildings as $building): ?>
                        <option value="<?= $building['id'] ?>" <?= ($searchData['building_id'] == $building['id']) ? 'selected' : '' ?>>
                            Корпус <?= htmlspecialchars($building['house_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-search">🔍 Найти свободные номера</button>
                </div>
            </form>
        </div>
        
        <?php if (!empty($searchData['check_in']) && !empty($searchData['check_out'])): ?>
            <div class="results-header">
                <h3 style="color: #1e4663;">📋 Доступные номера</h3>
                <div class="results-count">
                    Найдено: <strong><?= count($availableRooms) ?></strong> номеров
                </div>
            </div>
            
            <?php if (empty($availableRooms)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🏨</div>
                    <h3>Нет свободных номеров</h3>
                    <p>На выбранные даты нет свободных номеров. Попробуйте изменить даты.</p>
                </div>
            <?php else: ?>
                <div class="rooms-grid">
                    <?php foreach ($availableRooms as $room): ?>
                    <div class="room-card-search">
                        <div class="room-image" style="background-image: url('<?= $room['image'] ?>')">
                            <div class="room-badge">Свободен</div>
                        </div>
                        <div class="room-content">
                            <div class="room-header">
                                <div class="room-number">Номер <?= $room['room_number'] ?></div>
                                <div class="room-building">Корпус <?= htmlspecialchars($room['building_name']) ?></div>
                            </div>
                            <div class="room-description">
                                <?= htmlspecialchars($room['description']) ?>
                            </div>
                            <div class="room-features">
                                <span class="feature-tag">👥 Вместимость: <?= $room['num_place'] ?> <?= getPluralForm($room['num_place'], 'человек', 'человека', 'человек') ?></span>
                                <span class="feature-tag">📶 Wi-Fi</span>
                                <span class="feature-tag">📺 TV</span>
                                <span class="feature-tag">❄️ Кондиционер</span>
                            </div>
                            <div class="room-info">
                                <span>💰 Цена за ночь:</span>
                                <span class="room-price"><?= number_format($room['price'], 0, '', ' ') ?> ₽</span>
                            </div>
                            <button class="btn-book" data-room-id="<?= $room['id'] ?>" data-room-number="<?= $room['room_number'] ?>" data-building-name="<?= htmlspecialchars($room['building_name']) ?>" data-price="<?= $room['price'] ?>" data-beds="<?= $room['num_place'] ?>">
                                📅 Забронировать номер
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'footer.php'; ?>

<!-- Модальное окно для входа -->
<div id="loginPromptModal" class="login-prompt-modal">
    <div class="login-prompt-content">
        <div class="login-prompt-icon">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div class="login-prompt-title">🔐 Требуется авторизация</div>
        <div class="login-prompt-message">
            Для бронирования номера необходимо войти в систему.
        </div>
        <div class="login-prompt-buttons">
            <button class="btn-login-prompt" id="confirmLoginBtn">Войти</button>
            <button class="btn-cancel-prompt" id="cancelLoginBtn">Отмена</button>
        </div>
    </div>
</div>

<!-- Модальное окно входа -->
<div id="authModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" id="closeModalBtn">&times;</span>
        <h2>🔐 Вход в личный кабинет</h2>
        <form id="loginFormModal" method="POST" action="login.php">
            <input type="text" id="loginUsername" name="username" placeholder="ФИО гостя" required>
            <input type="password" id="loginPassword" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
            <div class="modal-footer">
                <a href="#">Забыли пароль?</a> | <a href="register.php">Регистрация</a>
            </div>
        </form>
    </div>
</div>

<script>
const today = new Date().toISOString().split('T')[0];
const checkIn = document.getElementById('check_in');
const checkOut = document.getElementById('check_out');

if (checkIn) checkIn.min = today;
if (checkOut) checkOut.min = today;

if (checkIn) {
    checkIn.addEventListener('change', function() {
        if (checkOut) checkOut.min = this.value;
    });
}

const loginPromptModal = document.getElementById('loginPromptModal');
const confirmLoginBtn = document.getElementById('confirmLoginBtn');
const cancelLoginBtn = document.getElementById('cancelLoginBtn');
let pendingRoomData = null;

function showLoginPrompt(roomData) {
    pendingRoomData = roomData;
    loginPromptModal.style.display = 'flex';
}

function hideLoginPrompt() {
    loginPromptModal.style.display = 'none';
}

function openMainLoginModal() {
    const modal = document.getElementById('authModal');
    if (modal) modal.style.display = 'flex';
}

if (confirmLoginBtn) {
    confirmLoginBtn.addEventListener('click', function() {
        hideLoginPrompt();
        openMainLoginModal();
    });
}

if (cancelLoginBtn) {
    cancelLoginBtn.addEventListener('click', function() {
        hideLoginPrompt();
        pendingRoomData = null;
    });
}

if (loginPromptModal) {
    window.onclick = function(e) {
        if (e.target === loginPromptModal) {
            hideLoginPrompt();
            pendingRoomData = null;
        }
    };
}

document.querySelectorAll('.btn-book').forEach(btn => {
    btn.addEventListener('click', function() {
        <?php if (isset($_SESSION['user_id'])): ?>
            const roomId = this.getAttribute('data-room-id');
            const roomNumber = this.getAttribute('data-room-number');
            const buildingName = this.getAttribute('data-building-name');
            const price = this.getAttribute('data-price');
            const beds = this.getAttribute('data-beds');
            window.location.href = `booking_form.php?room_id=${roomId}&room_name=Номер%20${roomNumber}&corpus_name=Корпус%20${encodeURIComponent(buildingName)}&price=${price}%20₽/ночь&beds=${beds}%20мест`;
        <?php else: ?>
            const roomData = {
                roomId: this.getAttribute('data-room-id'),
                roomNumber: this.getAttribute('data-room-number'),
                buildingName: this.getAttribute('data-building-name'),
                price: this.getAttribute('data-price'),
                beds: this.getAttribute('data-beds')
            };
            showLoginPrompt(roomData);
        <?php endif; ?>
    });
});

const loginBtnHeader = document.getElementById('loginBtnHeader');
const accountIconBtn = document.getElementById('accountIconBtn');

if (loginBtnHeader) {
    loginBtnHeader.addEventListener('click', function(e) {
        e.preventDefault();
        openMainLoginModal();
    });
}

if (accountIconBtn) {
    accountIconBtn.addEventListener('click', function(e) {
        e.preventDefault();
        openMainLoginModal();
    });
}

const closeModalBtn = document.getElementById('closeModalBtn');
if (closeModalBtn) {
    closeModalBtn.onclick = function() {
        const modal = document.getElementById('authModal');
        if (modal) modal.style.display = 'none';
    };
}

const authModal = document.getElementById('authModal');
if (authModal) {
    window.onclick = function(e) {
        if (e.target === authModal) {
            authModal.style.display = 'none';
        }
        if (e.target === loginPromptModal) {
            hideLoginPrompt();
            pendingRoomData = null;
        }
    };
}
</script>

</body>
</html>