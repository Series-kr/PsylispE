<?php
namespace App\Models;

class EmployeeSalaryComponent extends BaseModel {
    protected $table = 'employee_salary_components';

    public function getForEmployee($employeeId) {
        $query = "SELECT esc.*, sc.component_code, sc.name, sc.type, sc.calculation_type, sc.default_value
                  FROM {$this->table} esc
                  JOIN salary_components sc ON esc.component_id = sc.id
                  WHERE esc.employee_id = ? AND esc.tenant_id = ? AND esc.is_active = 1
                  ORDER BY sc.type, sc.component_code";
        
        $stmt = $this->execute($query, [$employeeId, $this->tenantId]);
        return $stmt->fetchAll();
    }

    public function assignComponent($employeeId, $componentId, $customValue = null) {
        $query = "INSERT INTO {$this->table} (tenant_id, employee_id, component_id, custom_value) 
                  VALUES (?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE custom_value = VALUES(custom_value), is_active = 1";
        
        $this->execute($query, [$this->tenantId, $employeeId, $componentId, $customValue]);
        return $this->pdo->lastInsertId();
    }

    public function deactivateComponent($employeeId, $componentId) {
        $query = "UPDATE {$this->table} SET is_active = 0 
                  WHERE employee_id = ? AND component_id = ? AND tenant_id = ?";
        
        $this->execute($query, [$employeeId, $componentId, $this->tenantId]);
    }
}