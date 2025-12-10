<?php
namespace App\Models;

class SalaryComponent extends BaseModel {
    protected $table = 'salary_components';

    public function getByType($type = null) {
        $query = "SELECT * FROM {$this->table} WHERE tenant_id = ?";
        $params = [$this->tenantId];

        if ($type) {
            $query .= " AND type = ?";
            $params[] = $type;
        }

        $query .= " ORDER BY type, component_code";
        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll();
    }

    public function getByCode($componentCode) {
        $query = "SELECT * FROM {$this->table} WHERE tenant_id = ? AND component_code = ?";
        $stmt = $this->execute($query, [$this->tenantId, $componentCode]);
        return $stmt->fetch();
    }

    public function create($data) {
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        $this->execute($query, array_values($data));

        return $this->pdo->lastInsertId();
    }

    public function getForEmployee($employeeId) {
        // Get components assigned to employee via salary structure
        $query = "SELECT sc.* 
                  FROM salary_components sc
                  INNER JOIN employee_salary_components esc ON sc.id = esc.component_id
                  WHERE esc.employee_id = ? AND sc.tenant_id = ?
                  ORDER BY sc.type, sc.component_code";
        
        $stmt = $this->execute($query, [$employeeId, $this->tenantId]);
        return $stmt->fetchAll();
    }
}