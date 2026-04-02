<?php
// functions.php - общие функции

function pluralForm($number, $one, $two, $five) {
    $n = abs($number) % 100;
    if ($n >= 5 && $n <= 20) return $five;
    $n %= 10;
    if ($n === 1) return $one;
    if ($n >= 2 && $n <= 4) return $two;
    return $five;
}

function formatPrice($price) {
    return number_format($price, 0, '', ' ') . ' ₽';
}

function formatDate($date, $format = 'd.m.Y') {
    return date($format, strtotime($date));
}

function getMealPlanText($plan) {
    $plans = [
        'none' => 'Без питания (0 ₽/день)',
        'breakfast' => 'Завтрак (шведский стол) — +800 ₽/день',
        'half_board' => 'Полупансион (завтрак + ужин) — +1500 ₽/день',
        'full_board' => 'Полный пансион (завтрак + обед + ужин) — +2200 ₽/день'
    ];
    return $plans[$plan] ?? 'Не выбрано';
}

function getServiceName($service) {
    $names = [
        'parking' => '🚗 Парковка (500 ₽/день)',
        'spa' => '💆‍♀️ СПА-центр (1500 ₽)',
        'transfer' => '🚐 Трансфер от аэропорта (2500 ₽)',
        'excursion' => '🏛️ Экскурсионное обслуживание (3000 ₽)'
    ];
    return $names[$service] ?? $service;
}

function getOptionName($option) {
    $names = [
        'pets' => '🐕 Размещение с питомцем (1000 ₽/день)',
        'baby_cot' => '👶 Детская кроватка (бесплатно)',
        'late_checkout' => '⏰ Поздний выезд до 18:00 (1500 ₽)',
        'early_checkin' => '🌅 Ранний заезд с 8:00 (1000 ₽)',
        'room_service' => '🛎️ Завтрак в номер (300 ₽/день)'
    ];
    return $names[$option] ?? $option;
}