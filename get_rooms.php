<?php
require_once 'models/Building.php';

$buildingId = $_GET['building_id'] ?? 0;

if ($buildingId) {
    $buildingModel = new Building();
    $rooms = $buildingModel->getRooms($buildingId);
    echo json_encode($rooms);
} else {
    echo json_encode([]);
}
?>