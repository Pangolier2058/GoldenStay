<?php
// profile.php

session_start();
require_once 'models/Visitor.php';
require_once 'models/Booking.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$visitorModel = new Visitor();
$bookingModel = new Booking();

$visitor = $visitorModel->getById($_SESSION['user_id']);
$bookings = $bookingModel->getByVisitor($_SESSION['user_id']);

$totalBookings = count($bookings);
$activeBookings = 0;

foreach ($bookings as $booking) {
    if (!in_array($booking['status'], ['cancelled', 'completed'])) {
        $activeBookings++;
    }
}
?>
<?php include 'header.php'; ?>

<main>
    <div class="container py-4">
        <!-- Карточка профиля -->
        <div class="card border-0 shadow-lg rounded-4 mb-5">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-md-8 d-flex align-items-center gap-4">
                        <div class="bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-person-fill text-white fs-1"></i>
                        </div>
                        <div>
                            <h1 class="h3 mb-2 text-dark"><?= htmlspecialchars($visitor['name']) ?></h1>
                            <p class="mb-1 text-secondary"><i class="bi bi-envelope me-2"></i> <?= htmlspecialchars($visitor['email'] ?? 'Не указан') ?></p>
                            <p class="mb-0 text-secondary"><i class="bi bi-telephone me-2"></i> <?= htmlspecialchars($visitor['phone'] ?? 'Не указан') ?></p>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="logout.php" class="btn btn-danger rounded-pill px-4">
                            <i class="bi bi-box-arrow-right"></i> Выйти
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Статистика -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-journal-bookmark-fill fs-1 text-warning"></i>
            <h2 class="mb-0">Мои бронирования</h2>
        </div>
        
        <div class="row g-3 mb-4">
            <div class="col-sm-6">
                <div class="card border-0 bg-light rounded-4 text-center p-3">
                    <div class="display-4 fw-bold text-warning"><?= $totalBookings ?></div>
                    <div class="text-secondary">Всего бронирований</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card border-0 bg-light rounded-4 text-center p-3">
                    <div class="display-4 fw-bold text-warning"><?= $activeBookings ?></div>
                    <div class="text-secondary">Активных бронирований</div>
                </div>
            </div>
        </div>
        
        <!-- Список бронирований -->
        <?php if (empty($bookings)): ?>
            <div class="card border-0 bg-white rounded-4 text-center p-5 shadow-sm">
                <i class="bi bi-calendar-x fs-1 text-secondary mb-3"></i>
                <h3 class="h4 text-dark mb-2">У вас пока нет бронирований</h3>
                <p class="text-secondary mb-4">Начните планировать свой отдых уже сегодня!</p>
                <a href="search_rooms.php" class="btn btn-success rounded-pill px-4 py-2 align-self-center">🔍 Забронировать номер</a>
            </div>
        <?php else: ?>
            <?php foreach ($bookings as $booking): 
                $start = new DateTime($booking['start_date']);
                $end = new DateTime($booking['end_date']);
                $days = $start->diff($end)->days;
                $total = $days * $booking['price'];
                $currentDate = new DateTime();
                
                if ($booking['status'] == 'pending') {
                    $status = ['text' => 'Ожидает подтверждения', 'class' => 'status-pending'];
                } elseif ($booking['status'] == 'confirmed') {
                    $status = ($currentDate > $end) 
                        ? ['text' => 'Завершено', 'class' => 'status-completed']
                        : ['text' => 'Подтверждено', 'class' => 'status-confirmed'];
                } elseif ($booking['status'] == 'cancelled') {
                    $status = ['text' => 'Отменено', 'class' => 'status-cancelled'];
                } else {
                    $status = ['text' => 'Завершено', 'class' => 'status-completed'];
                }
            ?>
            <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
                <div class="px-3 py-2 fw-bold text-center <?= $status['class'] ?>"><?= $status['text'] ?></div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="h5 fw-bold text-dark mb-2">🏠 Номер <?= $booking['room_number'] ?></h3>
                            <span class="badge bg-dark text-warning rounded-pill px-3 py-2 mb-3"><?= $booking['building_name'] ?></span>
                            <div class="d-flex flex-wrap gap-3 mt-2">
                                <span class="text-secondary"><i class="bi bi-people me-1"></i> <?= $booking['num_persons'] ?> <?= pluralForm($booking['num_persons'], 'гость', 'гостя', 'гостей') ?></span>
                                <span class="text-secondary"><i class="bi bi-cash me-1"></i> <?= number_format($booking['price'], 0, '', ' ') ?> ₽/ночь</span>
                            </div>
                        </div>
                        <div class="col-md-4 mt-3 mt-md-0 text-md-end">
                            <p class="mb-1"><strong>📅 Заезд:</strong> <?= date('d.m.Y', strtotime($booking['start_date'])) ?></p>
                            <p class="mb-1"><strong>🚪 Выезд:</strong> <?= date('d.m.Y', strtotime($booking['end_date'])) ?></p>
                            <p class="mb-2"><strong>⏱️ Дней:</strong> <?= $days ?></p>
                            <div class="bg-dark d-inline-block rounded-pill px-3 py-2 mb-2">
                                <span class="text-warning fw-bold">💰 <?= number_format($total, 0, '', ' ') ?> ₽</span>
                            </div>
                            <?php if (in_array($booking['status'], ['pending', 'confirmed'])): ?>
                                <div>
                                    <button class="btn btn-danger btn-sm rounded-pill px-3" onclick="cancelBooking(<?= $booking['id'] ?>)">Отменить бронирование</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'footer.php'; ?>

<!-- Модальное окно отмены -->
<div id="cancelModal" class="modal" style="display: none;">
    <div class="modal-content bg-white rounded-4 p-4" style="max-width: 480px; width: 90%;">
        <span class="close-modal" id="closeCancelModal">&times;</span>
        <div class="text-center">
            <i class="bi bi-exclamation-triangle-fill fs-1 text-warning mb-3"></i>
            <h3 class="h5 fw-bold mb-2">Отмена бронирования</h3>
            <p class="text-secondary mb-4">Вы уверены, что хотите отменить это бронирование?</p>
            <div class="d-flex gap-3 justify-content-center">
                <button id="confirmCancelBtn" class="btn btn-danger px-3 py-2 rounded-pill">Да, отменить</button>
                <button id="closeConfirmModal" class="btn btn-secondary px-3 py-2 rounded-pill">Нет, оставить</button>
            </div>
        </div>
    </div>
</div>

<style>
.status-pending { background: #fff3cd; color: #856404; }
.status-confirmed { background: #d4edda; color: #155724; }
.status-cancelled { background: #f8d7da; color: #721c24; }
.status-completed { background: #cce5ff; color: #004085; }
</style>

<script>
let currentBookingId = null;

function cancelBooking(id) {
    currentBookingId = id;
    document.getElementById('cancelModal').style.display = 'flex';
}

const modal = document.getElementById('cancelModal');
const closeModal = () => {
    if (modal) modal.style.display = 'none';
    currentBookingId = null;
};

document.getElementById('closeCancelModal')?.addEventListener('click', closeModal);
document.getElementById('closeConfirmModal')?.addEventListener('click', closeModal);
window.onclick = (e) => { if (e.target === modal) closeModal(); };

document.getElementById('confirmCancelBtn')?.addEventListener('click', function() {
    if (!currentBookingId) return;
    
    fetch('cancel_booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'booking_id=' + currentBookingId
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const alert = document.createElement('div');
            alert.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3';
            alert.style.zIndex = '9999';
            alert.innerHTML = '<i class="bi bi-check-circle-fill"></i> ✅ Бронирование отменено';
            document.body.appendChild(alert);
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(() => alert('Ошибка при отмене'));
    
    closeModal();
});
</script>
</body>
</html>