<?php
namespace App\Models;

class EmployeeSalary extends BaseModel {
    protected $table = 'employee_salary';

    public function getCurrentSalary($employeeId) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE employee_id = ? AND tenant_id = ?
                  AND effective_from <= CURDATE() 
                  AND (effective_to IS NULL OR effective_to >= CURDATE())
                  ORDER BY effective_from DESC 
                  LIMIT 1";
        
        $stmt = $this->execute($query, [$employeeId, $this->tenantId]);
        return $stmt->fetch();
    }

    public function createSalary($employeeId, $baseSalary, $effectiveFrom) {
        $query = "INSERT INTO {$this->table} (tenant_id, employee_id, base_salary, effective_from) 
                  VALUES (?, ?, ?, ?)";
        
        $this->execute($query, [$this->tenantId, $employeeId, $baseSalary, $effectiveFrom]);
        return $this->pdo->lastInsertId();
    }

    public function getSalaryHistory($employeeId) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE employee_id = ? AND tenant_id = ?
                  ORDER BY effective_from DESC";
        
        $stmt = $this->execute($query, [$employeeId, $this->tenantId]);
        return $stmt->fetchAll();
    }
}