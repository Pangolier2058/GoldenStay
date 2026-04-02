<?php
// models/FileStorage.php

require_once __DIR__ . '/../config/database.php';

class FileStorage {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // ========== ИЗОБРАЖЕНИЯ КОРПУСОВ ==========
    
    public function getBuildingImage($buildingId) {
        $query = "SELECT file_path FROM building_image WHERE building_id = :building_id ORDER BY id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':building_id', $buildingId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['file_path'] : null;
    }
    
    public function saveBuildingImage($buildingId, $fileName, $filePath) {
        $query = "INSERT INTO building_image (building_id, file_name, file_path) VALUES (:building_id, :file_name, :file_path)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':building_id', $buildingId);
        $stmt->bindParam(':file_name', $fileName);
        $stmt->bindParam(':file_path', $filePath);
        return $stmt->execute();
    }
    
    // ========== ИЗОБРАЖЕНИЯ НОМЕРОВ ==========
    
    public function getRoomImage($roomId) {
        $query = "SELECT file_path FROM room_image WHERE room_id = :room_id ORDER BY id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_id', $roomId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['file_path'] : null;
    }
    
    public function saveRoomImage($roomId, $fileName, $filePath) {
        $query = "INSERT INTO room_image (room_id, file_name, file_path) VALUES (:room_id, :file_name, :file_path)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_id', $roomId);
        $stmt->bindParam(':file_name', $fileName);
        $stmt->bindParam(':file_path', $filePath);
        return $stmt->execute();
    }
    
    // ========== ОБЩИЙ МЕТОД ЗАГРУЗКИ ==========
    
    public function uploadFile($file, $type, $entityId) {
        $uploadDir = __DIR__ . '/../uploads/';
        
        if ($type === 'building') {
            $subDir = 'images/';
        } elseif ($type === 'room') {
            $subDir = 'images/';
        } else {
            return false;
        }
        
        $fullUploadDir = $uploadDir . $subDir;
        if (!file_exists($fullUploadDir)) {
            mkdir($fullUploadDir, 0777, true);
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $ext;
        $destination = $fullUploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $relativePath = 'uploads/' . $subDir . $filename;
            
            if ($type === 'building') {
                $this->saveBuildingImage($entityId, $file['name'], $relativePath);
            } elseif ($type === 'room') {
                $this->saveRoomImage($entityId, $file['name'], $relativePath);
            }
            
            return $relativePath;
        }
        
        return false;
    }
}
?>