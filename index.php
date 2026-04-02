<?php
// index.php

session_start();
require_once 'models/Building.php';
require_once 'models/Visitor.php';
require_once 'functions.php';

$buildingModel = new Building();
$buildings = $buildingModel->getAll();

$buildingsData = [];

foreach ($buildings as $building) {
    $rooms = $buildingModel->getRooms($building['id']);
    $prices = array_column($rooms, 'price');
    $minPrice = !empty($prices) ? min($prices) : 0;
    
    // Характеристики для разных корпусов
    $features = [
        1 => ['distance' => 50, 'rating' => '⭐ 4.7', 'desc' => 'Уединение и свежий воздух. Идеален для спокойного отдыха и работы в тишине.',
              'fullDesc' => 'Северный корпус расположен в самой тихой части комплекса, окружённой сосновым парком. Идеальный выбор для тех, кто ценит покой и уединение. Корпус оснащён современными коворкингами для работы на природе, а также прачечной для длительного проживания.',
              'amenities' => ['🌿 Парковая зона', '☕ Коворкинг', '🧺 Прачечная', '🚲 Прокат велосипедов'],
              'roomFeatures' => [
                  ['area' => '22 м²', 'features' => '🌲 Лесной вид, ортопедическая кровать, чайная станция'],
                  ['area' => '30 м²', 'features' => '🌿 Балкон с видом на парк, кресло-качалка, кофеварка'],
                  ['area' => '45 м²', 'features' => '🔥 Гостиная зона, камин, панорамные окна в парк']
              ]],
        2 => ['distance' => 100, 'rating' => '⭐ 4.9', 'desc' => 'Ближайший к бассейну, СПА-центру и термальным источникам. Энергия солнца.',
              'fullDesc' => 'Южный корпус — это центр wellness-отдыха. Всего в 50 метрах расположен подогреваемый бассейн и СПА-комплекс с термальными источниками. Номера оформлены в солнечных тонах, большинство имеют балконы с видом на бассейн. Для гостей работает бар у воды и круглосуточный фитнес-центр.',
              'amenities' => ['🏊 Бассейн подогрев', '💆‍♀️ СПА-комплекс', '🍹 Бар у воды', '🏋️ Фитнес-центр'],
              'roomFeatures' => [
                  ['area' => '18 м²', 'features' => '🏊 Вид на бассейн, кондиционер, халаты и тапочки'],
                  ['area' => '28 м²', 'features' => '☀️ Терраса с шезлонгами, мини-бар, доступ в СПА'],
                  ['area' => '55 м²', 'features' => '💆‍♀️ Приватная сауна, джакузи, вид на термальные источники']
              ]],
        3 => ['distance' => 150, 'rating' => '⭐ 4.8', 'desc' => 'Панорамные окна на закаты над морем. Романтика и роскошь в каждой детали.',
              'fullDesc' => 'Западный корпус славится своими панорамными окнами и захватывающими видами на морские закаты. Каждый номер имеет уникальную планировку, а в люксах установлены джакузи. Для гостей работает частный кинотеатр и лаунж-зона с видом на море.',
              'amenities' => ['🌅 Вид на море', '🛁 Джакузи в люксах', '🎥 Кинотеатр', '🍷 Лаунж-бар'],
              'roomFeatures' => [
                  ['area' => '25 м²', 'features' => '🌊 Вид на море, романтическое освещение, винный бар'],
                  ['area' => '35 м²', 'features' => '🌅 Панорамное окно во всю стену, шезлонги на балконе'],
                  ['area' => '70 м²', 'features' => '🛁 Джакузи у окна, кровать King-size, частный кинотеатр']
              ]],
        4 => ['distance' => 200, 'rating' => '⭐ 4.6', 'desc' => 'Семейный рай: тихий двор, сад, детские площадки и анимация.',
              'fullDesc' => 'Восточный корпус создан специально для семей с детьми. На территории оборудованы современные детские площадки, работает анимационная команда и проводятся мастер-классы. В корпусе предусмотрены семейные номера с кухонными зонами и двухуровневые апартаменты.',
              'amenities' => ['👨‍👩‍👧‍👦 Детская комната', '🎨 Мастер-классы', '🏸 Спортплощадка', '🍼 Детское меню'],
              'roomFeatures' => [
                  ['area' => '35 м²', 'features' => '👶 Детская кроватка, игрушки, мультфильмы по ТВ'],
                  ['area' => '42 м²', 'features' => '🍼 Две спальни, кухонный уголок, посудомойка'],
                  ['area' => '65 м²', 'features' => '🎮 Двухуровневый номер, детская игровая зона, консоль']
              ]],
        5 => ['distance' => 250, 'rating' => '⭐ 5.0', 'desc' => 'Сердце комплекса: лобби-бар, ресторан высокой кухни и лаунж-зона.',
              'fullDesc' => 'Центральный корпус — это визитная карточка Golden Stay. Здесь расположены главный ресторан высокой кухни с авторским меню, элегантный лобби-бар и лаунж-зона с живой музыкой по вечерам. Номера выполнены в премиальном дизайне с использованием натуральных материалов.',
              'amenities' => ['🍽️ Ресторан Michelin', '🥂 Лаунж-бар', '🎵 Живая музыка', '🅿️ VIP-парковка'],
              'roomFeatures' => [
                  ['area' => '40 м²', 'features' => '✨ Дизайнерский интерьер, мини-бар, кофемашина'],
                  ['area' => '60 м²', 'features' => '🥂 Гостиная + спальня, консьерж-сервис, шампанское'],
                  ['area' => '120 м²', 'features' => '👑 Личный лифт, терраса 360°, личный повар']
              ]]
    ];
    
    $f = $features[$building['id']] ?? $features[1];
    
    $roomsData = [];
    foreach ($rooms as $index => $room) {
        $idx = $index % 3;
        $roomsData[] = [
            'id' => $room['id'],
            'name' => 'Номер ' . $room['room_number'],
            'subtitle' => $room['num_place'] . ' мест',
            'beds' => $room['num_place'] . ' ' . pluralForm($room['num_place'], 'кровать', 'кровати', 'кроватей'),
            'price' => formatPrice($room['price']),
            'available' => true,
            'area' => $f['roomFeatures'][$idx]['area'],
            'features' => $f['roomFeatures'][$idx]['features'],
            'image' => $room['image']
        ];
    }
    
    $buildingsData[] = [
        'id' => $building['id'],
        'name' => 'Корпус ' . $building['house_name'],
        'description' => $f['desc'],
        'fullDescription' => $f['fullDesc'],
        'image' => $building['image'],
        'rating' => $f['rating'],
        'distance' => $f['distance'] . ' м до пляжа',
        'amenities' => $f['amenities'],
        'priceRange' => 'от ' . formatPrice($minPrice),
        'rooms' => $roomsData
    ];
}
?>
<?php include 'header.php'; ?>

<!-- Системные сообщения -->
<?php if (isset($_SESSION['login_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show m-3">
        <i class="bi bi-check-circle-fill"></i> <?= $_SESSION['login_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['login_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['logout_message'])): ?>
    <div class="alert alert-info alert-dismissible fade show m-3">
        <i class="bi bi-info-circle-fill"></i> <?= $_SESSION['logout_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['logout_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['login_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show m-3">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= $_SESSION['login_error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['login_error']); ?>
<?php endif; ?>

<main>
    <div id="mainPage" class="container py-4">
        <div class="hero-custom rounded-4 p-5 mb-5 text-center shadow-sm">
            <h1 class="fw-bold text-primary">Роскошь и уют в <span class="text-warning">5 корпусах</span> Golden Stay</h1>
            <p class="lead text-secondary mt-3 mx-auto" style="max-width: 800px;">Выберите идеальный корпус: от тихого паркового до центрального с панорамным видом. В каждом — уникальные номера и сервис премиум-класса.</p>
        </div>

        <div id="buildings" class="mb-4">
            <h2 class="mb-0 fs-1">🏢 Наши корпуса — больше, чем просто номер</h2>
        </div>
        
        <div class="buildings-grid">
            <?php foreach ($buildingsData as $building): ?>
            <div class="building-card" data-id="<?= $building['id'] ?>">
                <div class="building-img" style="background-image: url('<?= $building['image'] ?>'); background-size: cover; background-position: center;">
                    <div class="badge-corpus"><?= $building['name'] ?></div>
                    <div class="rating-badge"><?= $building['rating'] ?></div>
                </div>
                <div class="building-info">
                    <div class="building-header">
                        <h3 class="building-name"><?= htmlspecialchars($building['name']) ?></h3>
                        <span class="building-distance"><?= $building['distance'] ?></span>
                    </div>
                    <p class="building-desc"><?= htmlspecialchars($building['description']) ?></p>
                    
                    <div class="amenities-list">
                        <?php foreach ($building['amenities'] as $amenity): ?>
                            <span class="amenity-tag"><?= htmlspecialchars($amenity) ?></span>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="price-wrapper">
                        <span class="price-badge">💰 <?= $building['priceRange'] ?></span>
                    </div>
                    
                    <div class="rooms-preview">
                        <?php foreach (array_slice($building['rooms'], 0, 3) as $room): ?>
                            <span class="room-tag"><?= htmlspecialchars($room['name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="card-footer-custom">
                        <button class="select-btn" data-id="<?= $building['id'] ?>">🔍 Выбрать корпус и номера</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="detailPage" class="container py-4" style="display: none;">
        <button class="btn btn-primary rounded-pill px-4 py-2 mb-4" id="backToMain">
            <i class="bi bi-arrow-left"></i> Назад к списку корпусов
        </button>
        <div id="corpusDetailContent"></div>
    </div>
</main>

<?php include 'footer.php'; ?>

<!-- Модальное окно входа -->
<div id="authModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); z-index: 2000; align-items: center; justify-content: center;">
    <div class="modal-content bg-white rounded-4 p-4" style="max-width: 420px; width: 90%;">
        <span class="close-modal" id="closeModalBtn" style="position: absolute; top: 15px; right: 20px; font-size: 1.8rem; cursor: pointer;">&times;</span>
        <h2 class="text-center mb-4">🔐 Вход в личный кабинет</h2>
        <form id="loginFormModal" onsubmit="return false;">
            <div class="mb-3">
                <input type="text" id="loginUsername" class="form-control rounded-pill py-2 px-3" placeholder="ФИО/почта гостя" required>
            </div>
            <div class="mb-3">
                <input type="password" id="loginPassword" class="form-control rounded-pill py-2 px-3" placeholder="Пароль" required>
            </div>
            <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 fw-bold">Войти</button>
            <div class="text-center mt-3">
                <a href="#" class="text-decoration-none small">Забыли пароль?</a> | 
                <a href="register.php" class="text-decoration-none small">Регистрация</a>
            </div>
        </form>
    </div>
</div>

<script>
const buildingsData = <?= json_encode($buildingsData, JSON_UNESCAPED_UNICODE) ?>;
console.log('=== ДАННЫЕ КОРПУСОВ ===');
console.log('Количество корпусов:', buildingsData.length);

function showMainPage() { 
    window.location.href = 'index.php'; 
}

function goToCorpuses() { 
    const section = document.getElementById('buildings');
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
    } else {
        window.location.href = 'index.php#buildings';
    }
}

// Навигация
document.addEventListener('DOMContentLoaded', function() {
    const navHome = document.getElementById('navHome');
    const navCorpuses = document.getElementById('navCorpuses');
    
    if (navHome) navHome.addEventListener('click', showMainPage);
    if (navCorpuses) navCorpuses.addEventListener('click', goToCorpuses);
    
    // Модальное окно входа
    const authModal = document.getElementById('authModal');
    const loginBtn = document.getElementById('loginBtnHeader');
    const closeModal = document.getElementById('closeModalBtn');
    
    if (loginBtn) {
        loginBtn.addEventListener('click', function() {
            if (authModal) authModal.style.display = 'flex';
        });
    }
    
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            if (authModal) authModal.style.display = 'none';
        });
    }
    
    if (authModal) {
        window.addEventListener('click', function(e) {
            if (e.target === authModal) authModal.style.display = 'none';
        });
    }
    
    // AJAX вход
    const loginForm = document.getElementById('loginFormModal');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = document.getElementById('loginUsername').value.trim();
            const password = document.getElementById('loginPassword').value;
            
            if (!username || !password) {
                alert('Введите логин и пароль');
                return;
            }
            
            const btn = this.querySelector('button');
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Вход...';
            btn.disabled = true;
            
            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    username: username,
                    password: password,
                    redirect: window.location.pathname
                })
            })
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = original;
                btn.disabled = false;
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                btn.innerHTML = original;
                btn.disabled = false;
                alert('Ошибка соединения. Попробуйте позже.');
            });
        });
    }
    
    // Кнопки выбора корпуса
    const selectBtns = document.querySelectorAll('.select-btn');
    console.log('Найдено кнопок .select-btn:', selectBtns.length);
    
    selectBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = parseInt(this.getAttribute('data-id'));
            console.log('Клик по кнопке, ID корпуса:', id);
            showCorpusDetail(id);
        });
    });
    
    // Кнопка назад
    const backBtn = document.getElementById('backToMain');
    if (backBtn) {
        backBtn.addEventListener('click', showMainPage);
    }
});

// Функция для получения описания номера
function getRoomDescription(buildingId, numPlace, roomNumber) {
    const descriptions = {
        1: { // Северный корпус
            1: "Уютный одноместный номер с видом на сосновый парк. Идеален для спокойного отдыха и работы в тишине.",
            2: "Просторный номер с двумя раздельными кроватями. Из окна открывается живописный вид на парковую зону.",
            3: "Семейный номер с дополнительным спальным местом. Есть детская кроватка и игровая зона.",
            4: "Большой номер для компании. Две спальни, гостиная зона, балкон с видом на парк."
        },
        2: { // Южный корпус
            1: "Светлый номер с видом на бассейн. В номере: кондиционер, халаты и тапочки.",
            2: "Комфортабельный номер с балконом и видом на бассейн. Входит бесплатный доступ в СПА.",
            3: "Просторный номер с террасой и шезлонгами. Идеален для отдыха у бассейна.",
            4: "Люкс с видом на бассейн и горы. В номере: джакузи, мини-бар, приватная терраса."
        },
        3: { // Западный корпус
            1: "Романтический номер с видом на море. В номере: винный бар, романтическое освещение.",
            2: "Номер с панорамным окном во всю стену. Наслаждайтесь закатами не выходя из кровати.",
            3: "Просторный номер с балконом и шезлонгами. Идеален для пар и медового месяца.",
            4: "Президентский люкс с джакузи у окна и видом на море. Кровать King-size, частный кинотеатр."
        },
        4: { // Восточный корпус
            1: "Уютный номер для одного. Есть рабочая зона и доступ в коворкинг.",
            2: "Семейный номер с детской кроваткой. В номере: игрушки, мультфильмы по ТВ.",
            3: "Двухкомнатный номер с кухонным уголком. Идеален для семьи с двумя детьми.",
            4: "Двухуровневые апартаменты с детской игровой зоной и консолью."
        },
        5: { // Центральный корпус
            1: "Дизайнерский номер в центре комплекса. В номере: мини-бар, кофемашина.",
            2: "Просторный номер с гостиной зоной. Входит консьерж-сервис и шампанское.",
            3: "Люкс с панорамным видом на город. Личный консьерж, доступ в лаунж-бар.",
            4: "Пентхаус с террасой 360°. Личный повар, приватный лифт, VIP-обслуживание."
        }
    };
    
    if (descriptions[buildingId] && descriptions[buildingId][numPlace]) {
        return descriptions[buildingId][numPlace];
    }
    
    const defaultDesc = {
        1: "Уютный номер с современной мебелью. В номере: удобная кровать, кондиционер, телевизор, Wi-Fi.",
        2: "Просторный номер с двумя кроватями. Идеален для пары или командированных. Есть рабочая зона.",
        3: "Семейный номер с дополнительным спальным местом. Подходит для отдыха с детьми.",
        4: "Большой номер для компании. Две спальни, гостиная, полностью оборудованная кухня."
    };
    const key = Math.min(numPlace, 4);
    return defaultDesc[key] || defaultDesc[1];
}

function showCorpusDetail(corpusId) {
    console.log('showCorpusDetail вызван с ID:', corpusId);
    
    if (typeof buildingsData === 'undefined') {
        alert('Ошибка загрузки данных.');
        return;
    }
    
    const building = buildingsData.find(b => parseInt(b.id) === parseInt(corpusId));
    if (!building) {
        alert('Корпус не найден');
        return;
    }
    
    const mainPage = document.getElementById('mainPage');
    const detailPage = document.getElementById('detailPage');
    const corpusDetailContent = document.getElementById('corpusDetailContent');
    
    // Получаем количество мест из subtitle
    const getNumPlace = (subtitle) => {
        const match = subtitle.match(/\d+/);
        return match ? parseInt(match[0]) : 2;
    };
    
    const roomsHtml = building.rooms.map(room => {
        const numPlace = getNumPlace(room.subtitle);
        const description = getRoomDescription(building.id, numPlace, room.name);
        return `
            <div class="room-card-detail">
                <div class="room-image-detail" style="background-image: url('${room.image}'); background-size: cover; background-position: center;">
                    <div class="room-badge-detail">${room.available ? 'Свободен' : 'Забронирован'}</div>
                </div>
                <div class="room-content-detail">
                    <div class="room-header-detail">
                        <div class="room-number-detail">${room.name}</div>
                        <div class="room-building-detail">${building.name}</div>
                    </div>
                    <div class="room-description-detail">
                        ${description}
                    </div>
                    <div class="room-features-detail">
                        <span class="feature-tag-detail">👥 Вместимость: ${room.subtitle}</span>
                        <span class="feature-tag-detail">📶 Wi-Fi</span>
                        <span class="feature-tag-detail">📺 TV</span>
                        <span class="feature-tag-detail">❄️ Кондиционер</span>
                    </div>
                    <div class="room-info-detail">
                        <span>💰 Цена за ночь:</span>
                        <span class="room-price-detail">${room.price}</span>
                    </div>
                    <button class="btn-book-detail" 
                            data-room-id="${room.id}" 
                            data-room-name="${room.name}" 
                            data-corpus-name="${building.name}" 
                            data-price="${room.price}"
                            data-beds="${room.beds}"
                            ${!room.available ? 'disabled' : ''}>
                        ${room.available ? '📅 Забронировать номер' : '❌ Нет свободных мест'}
                    </button>
                </div>
            </div>
        `;
    }).join('');
    
    corpusDetailContent.innerHTML = `
        <style>
            .rooms-grid-detail {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
                gap: 2rem;
                margin-top: 1rem;
            }
            .room-card-detail {
                background: white;
                border-radius: 28px;
                overflow: hidden;
                border: 1px solid #eef2f5;
                transition: transform 0.3s, box-shadow 0.3s;
                display: flex;
                flex-direction: column;
                height: 100%;
            }
            .room-card-detail:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 35px -12px rgba(0,0,0,0.2);
            }
            .room-image-detail {
                height: 220px;
                background-size: cover;
                background-position: center;
                position: relative;
            }
            .room-badge-detail {
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
            .room-content-detail {
                padding: 1.5rem;
                flex: 1;
                display: flex;
                flex-direction: column;
            }
            .room-header-detail {
                margin-bottom: 1rem;
                border-bottom: 2px solid #f7c948;
                padding-bottom: 0.8rem;
            }
            .room-number-detail {
                font-size: 1.5rem;
                font-weight: bold;
                color: #1e4663;
                margin-bottom: 0.3rem;
            }
            .room-building-detail {
                font-size: 0.85rem;
                color: #f7c948;
                background: #1e2a3a;
                display: inline-block;
                padding: 0.2rem 0.8rem;
                border-radius: 20px;
            }
            .room-description-detail {
                color: #4a627a;
                font-size: 0.9rem;
                line-height: 1.5;
                margin-bottom: 1rem;
                flex: 1;
            }
            .room-features-detail {
                display: flex;
                gap: 0.8rem;
                flex-wrap: wrap;
                margin-bottom: 1rem;
            }
            .feature-tag-detail {
                background: #f1f5f9;
                border-radius: 20px;
                padding: 0.3rem 0.8rem;
                font-size: 0.75rem;
                color: #2c5a6e;
            }
            .room-info-detail {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.8rem;
                padding: 0.5rem 0;
                border-top: 1px solid #eef2f5;
                border-bottom: 1px solid #eef2f5;
            }
            .room-price-detail {
                font-size: 1.3rem;
                font-weight: bold;
                color: #f7c948;
                background: #1e2a3a;
                display: inline-block;
                padding: 0.3rem 1rem;
                border-radius: 30px;
            }
            .btn-book-detail {
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
            .btn-book-detail:hover {
                background: #1f7a1f;
                transform: scale(1.02);
            }
            .btn-book-detail:disabled {
                opacity: 0.5;
                cursor: not-allowed;
                background: #95a5a6;
            }
            @media (max-width: 768px) {
                .rooms-grid-detail {
                    grid-template-columns: 1fr;
                }
                .room-image-detail {
                    height: 180px;
                }
                .room-number-detail {
                    font-size: 1.3rem;
                }
            }
        </style>
        <div class="corpus-detail bg-white rounded-4 overflow-hidden shadow-sm">
            <div class="corpus-detail-img" style="background-image: url('${building.image}'); background-size: cover; background-position: center; height: 500px; position: relative;">
                <div class="badge-corpus" style="position: absolute; top: 20px; right: 20px;">${building.name}</div>
            </div>
            <div class="p-4 p-md-5">
                <h1 class="display-4 fw-bold text-dark mb-3">${building.name}</h1>
                <div class="d-flex flex-wrap gap-3 mb-4 pb-3 border-bottom">
                    <div class="meta-item" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #f1f5f9; padding: 0.5rem 1.2rem; border-radius: 40px;">${building.rating}</div>
                    <div class="meta-item" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #f1f5f9; padding: 0.5rem 1.2rem; border-radius: 40px;">📍 ${building.distance}</div>
                    <div class="meta-item" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #f1f5f9; padding: 0.5rem 1.2rem; border-radius: 40px;">💰 ${building.priceRange}</div>
                </div>
                <p class="fs-5 text-secondary mb-4" style="line-height: 1.6;">${building.fullDescription}</p>
                <div class="d-flex flex-wrap gap-2 mb-4 py-3 border-top border-bottom">
                    ${building.amenities.map(a => `<span class="amenity-full" style="background: #f1f5f9; border-radius: 30px; padding: 0.5rem 1rem; display: inline-block;">${a}</span>`).join('')}
                </div>
                <h2 class="h2 mb-4">🏨 Доступные номера</h2>
                <div class="rooms-grid-detail">
                    ${roomsHtml}
                </div>
            </div>
        </div>
    `;
    
    if (mainPage) mainPage.style.display = 'none';
    if (detailPage) {
        detailPage.style.display = 'block';
        detailPage.classList.add('active');
    }
    window.scrollTo({ top: 0 });
    
    setTimeout(() => {
        document.querySelectorAll('.btn-book-detail').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!this.disabled) {
                    const roomId = this.getAttribute('data-room-id');
                    const roomName = this.getAttribute('data-room-name');
                    const corpusName = this.getAttribute('data-corpus-name');
                    const price = this.getAttribute('data-price');
                    const beds = this.getAttribute('data-beds');
                    
                    window.location.href = `booking_form.php?room_id=${roomId}&room_name=${encodeURIComponent(roomName)}&corpus_name=${encodeURIComponent(corpusName)}&price=${price}&beds=${beds}%20мест`;
                }
            });
        });
    }, 100);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>