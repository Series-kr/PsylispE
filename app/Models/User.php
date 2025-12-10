<?php
namespace App\Models;

class User extends BaseModel {
    protected $table = 'users';

    public function create($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        $this->execute($query, array_values($data));

        return $this->pdo->lastInsertId();
    }

    public function findByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->execute($query, [$email]);
        return $stmt->fetch();
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function getByTenant($tenantId, $role = null) {
        $query = "SELECT * FROM {$this->table} WHERE tenant_id = ?";
        $params = [$tenantId];

        if ($role) {
            $query .= " AND role = ?";
            $params[] = $role;
        }

        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll();
    }
}