<?php
namespace App\Models;

class Department extends BaseModel {
    protected $table = 'departments';

    public function getAll() {
        $query = "SELECT d.*, e.first_name, e.last_name 
                  FROM {$this->table} d 
                  LEFT JOIN employees e ON d.manager_id = e.id 
                  WHERE d.tenant_id = ? 
                  ORDER BY d.name";
        $stmt = $this->execute($query, [$this->tenantId]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        $this->execute($query, array_values($data));

        return $this->pdo->lastInsertId();
    }

    public function getEmployees($departmentId) {
        $query = "SELECT * FROM employees 
                  WHERE tenant_id = ? AND department_id = ? AND is_active = 1 
                  ORDER BY first_name, last_name";
        $stmt = $this->execute($query, [$this->tenantId, $departmentId]);
        return $stmt->fetchAll();
    }
}