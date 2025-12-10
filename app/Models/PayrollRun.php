<?php
namespace App\Models;

class PayrollRun extends BaseModel {
    protected $table = 'payroll_runs';

    public function createRun($data) {
        $query = "INSERT INTO {$this->table} (tenant_id, run_month, status, run_by) VALUES (?, ?, 'draft', ?)";
        $this->execute($query, [$data['tenant_id'], $data['run_month'], $data['run_by']]);
        return $this->pdo->lastInsertId();
    }

    public function getByTenant($tenantId, $status = null) {
        $query = "SELECT pr.*, u.first_name as run_by_name 
                  FROM {$this->table} pr 
                  LEFT JOIN users u ON pr.run_by = u.id 
                  WHERE pr.tenant_id = ?";
        
        $params = [$tenantId];
        if ($status) {
            $query .= " AND pr.status = ?";
            $params[] = $status;
        }

        $query .= " ORDER BY pr.run_month DESC";
        
        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll();
    }

    public function updateStatus($runId, $status, $approvedBy = null) {
        $query = "UPDATE {$this->table} SET status = ?, approved_by = ?, updated_at = NOW() WHERE id = ?";
        $this->execute($query, [$status, $approvedBy, $runId]);
    }
}