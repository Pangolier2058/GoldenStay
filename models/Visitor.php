<?php
// models/Visitor.php

require_once __DIR__ . '/../config/database.php';

class Visitor {
    private $conn;
    private $table = 'visitor';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByName($name) {
        $query = "SELECT * FROM " . $this->table . " WHERE name = :name LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByLogin($login) {
        $query = "SELECT * FROM " . $this->table . " WHERE name = :login OR email = :login LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function login($username, $password) {
        $user = $this->findByLogin($username);
        return ($user && password_verify($password, $user['password'])) ? $user : false;
    }

    public function create($name, $hashedPassword, $email = null, $phone = null) {
        $check = $this->conn->prepare("SELECT id FROM {$this->table} WHERE name = :name OR email = :email");
        $check->execute([':name' => $name, ':email' => $email]);
        if ($check->rowCount() > 0) return false;
        
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (name, password, email, phone) VALUES (:name, :password, :email, :phone)");
        $success = $stmt->execute([
            ':name' => $name,
            ':password' => $hashedPassword,
            ':email' => $email,
            ':phone' => $phone
        ]);
        
        return $success ? $this->conn->lastInsertId() : false;
    }

    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        if (isset($data['name'])) {
            $fields[] = "name = :name";
            $params[':name'] = $data['name'];
        }
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }
        if (isset($data['phone'])) {
            $fields[] = "phone = :phone";
            $params[':phone'] = $data['phone'];
        }
        if (isset($data['password'])) {
            $fields[] = "password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($fields)) return false;
        
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id");
        return $stmt->execute($params);
    }
}
?>