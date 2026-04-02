<?php
// models/Booking.php

require_once __DIR__ . '/../config/database.php';

class Booking {
    private $db;
    private $table = 'booking';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    private function getConn() {
        return $this->db->getConnection();
    }
    
    public function create($roomId, $visitorId, $startDate, $endDate, $numPersons) {
        if (!$this->isRoomAvailable($roomId, $startDate, $endDate)) {
            return ['success' => false, 'message' => 'Номер уже забронирован на выбранные даты'];
        }
        
        $sql = "INSERT INTO {$this->table} (room_id, visitor_id, start_date, end_date, num_persons) 
                VALUES (:room_id, :visitor_id, :start_date, :end_date, :num_persons)";
        $stmt = $this->getConn()->prepare($sql);
        
        $success = $stmt->execute([
            ':room_id' => $roomId,
            ':visitor_id' => $visitorId,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':num_persons' => $numPersons
        ]);
        
        return $success 
            ? ['success' => true, 'message' => 'Бронирование успешно создано', 'id' => $this->getConn()->lastInsertId()]
            : ['success' => false, 'message' => 'Ошибка при создании бронирования'];
    }
    
    public function getByVisitor($visitorId) {
        $sql = "SELECT b.*, r.room_number, r.price, r.num_place, bld.house_name as building_name, bld.id as building_id
                FROM {$this->table} b
                JOIN room r ON b.room_id = r.id
                JOIN building bld ON r.building_id = bld.id
                WHERE b.visitor_id = :visitor_id
                ORDER BY b.start_date DESC";
        $stmt = $this->getConn()->prepare($sql);
        $stmt->execute([':visitor_id' => $visitorId]);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT b.*, r.room_number, r.price, r.num_place, bld.house_name as building_name
                FROM {$this->table} b
                JOIN room r ON b.room_id = r.id
                JOIN building bld ON r.building_id = bld.id
                WHERE b.id = :id";
        $stmt = $this->getConn()->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function getAll() {
        $sql = "SELECT b.*, r.room_number, r.price, v.name as visitor_name, bld.house_name as building_name
                FROM {$this->table} b
                JOIN room r ON b.room_id = r.id
                JOIN building bld ON r.building_id = bld.id
                JOIN visitor v ON b.visitor_id = v.id
                ORDER BY b.start_date DESC";
        $stmt = $this->getConn()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getActive() {
        $sql = "SELECT b.*, r.room_number, r.price, v.name as visitor_name, bld.house_name as building_name
                FROM {$this->table} b
                JOIN room r ON b.room_id = r.id
                JOIN building bld ON r.building_id = bld.id
                JOIN visitor v ON b.visitor_id = v.id
                WHERE b.end_date >= CURDATE() AND (b.status IS NULL OR b.status != 'cancelled')
                ORDER BY b.start_date ASC";
        $stmt = $this->getConn()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getByDateRange($startDate, $endDate) {
        $sql = "SELECT b.*, r.room_number, v.name as visitor_name, bld.house_name as building_name
                FROM {$this->table} b
                JOIN room r ON b.room_id = r.id
                JOIN building bld ON r.building_id = bld.id
                JOIN visitor v ON b.visitor_id = v.id
                WHERE (b.start_date <= :end_date AND b.end_date >= :start_date)
                ORDER BY b.start_date";
        $stmt = $this->getConn()->prepare($sql);
        $stmt->execute([':start_date' => $startDate, ':end_date' => $endDate]);
        return $stmt->fetchAll();
    }
    
    public function isRoomAvailable($roomId, $startDate, $endDate) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE room_id = :room_id 
                AND (start_date <= :end_date AND end_date >= :start_date)
                AND (status IS NULL OR status != 'cancelled')";
        $stmt = $this->getConn()->prepare($sql);
        $stmt->execute([
            ':room_id' => $roomId,
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);
        return $stmt->fetch()['count'] == 0;
    }
    
    public function cancel($bookingId, $userId = null) {
        if ($userId !== null) {
            $check = $this->getConn()->prepare("SELECT id FROM {$this->table} WHERE id = :id AND visitor_id = :user_id");
            $check->execute([':id' => $bookingId, ':user_id' => $userId]);
            if ($check->rowCount() == 0) {
                return ['success' => false, 'message' => 'Бронирование не найдено'];
            }
        }
        
        $stmt = $this->getConn()->prepare("UPDATE {$this->table} SET status = 'cancelled' WHERE id = :id");
        $success = $stmt->execute([':id' => $bookingId]);
        
        return $success 
            ? ['success' => true, 'message' => 'Бронирование отменено']
            : ['success' => false, 'message' => 'Ошибка при отмене бронирования'];
    }
    
    public function confirm($bookingId) {
        $stmt = $this->getConn()->prepare("UPDATE {$this->table} SET status = 'confirmed' WHERE id = :id");
        $success = $stmt->execute([':id' => $bookingId]);
        return $success 
            ? ['success' => true, 'message' => 'Бронирование подтверждено']
            : ['success' => false, 'message' => 'Ошибка при подтверждении бронирования'];
    }
    
    public function updateStatus($bookingId, $status) {
        $allowed = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (!in_array($status, $allowed)) {
            return ['success' => false, 'message' => 'Некорректный статус'];
        }
        
        $stmt = $this->getConn()->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id");
        $success = $stmt->execute([':status' => $status, ':id' => $bookingId]);
        
        return $success 
            ? ['success' => true, 'message' => 'Статус обновлен']
            : ['success' => false, 'message' => 'Ошибка при обновлении статуса'];
    }
}