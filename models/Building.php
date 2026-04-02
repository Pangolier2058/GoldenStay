<?php
// models/Building.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/FileStorage.php';

class Building {
    private $conn;
    private $table = 'building';
    private $fileStorage;
    
    // Изображения по умолчанию
    private $defaultBuildingImage = 'https://images.unsplash.com/photo-1568495248636-6432b97bd949?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80';
    private $defaultRoomImage = 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->fileStorage = new FileStorage();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $buildings = $stmt->fetchAll();
        
        foreach ($buildings as &$building) {
            $image = $this->fileStorage->getBuildingImage($building['id']);
            $building['image'] = $image ?: $this->defaultBuildingImage;
        }
        
        return $buildings;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $building = $stmt->fetch();
        
        if ($building) {
            $image = $this->fileStorage->getBuildingImage($building['id']);
            $building['image'] = $image ?: $this->defaultBuildingImage;
        }
        
        return $building;
    }

    public function getRooms($buildingId) {
        $query = "SELECT * FROM room WHERE building_id = :building_id ORDER BY price";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':building_id', $buildingId);
        $stmt->execute();
        $rooms = $stmt->fetchAll();
        
        foreach ($rooms as &$room) {
            $image = $this->fileStorage->getRoomImage($room['id']);
            $room['image'] = $image ?: $this->defaultRoomImage;
        }
        
        return $rooms;
    }
}
?>