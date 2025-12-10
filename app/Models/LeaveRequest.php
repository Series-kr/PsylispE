<?php
namespace App\Models;

class LeaveRequest extends BaseModel {
    protected $table = 'leave_requests';

    public function createRequest($data) {
        $query = "INSERT INTO {$this->table} (tenant_id, employee_id, leave_type, start_date, end_date, reason, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $this->execute($query, [
            $data['tenant_id'],
            $data['employee_id'],
            $data['leave_type'],
            $data['start_date'],
            $data['end_date'],
            $data['reason'] ?? null,
            $data['status'] ?? 'pending'
        ]);

        return $this->pdo->lastInsertId();
    }

    public function getByEmployee($employeeId, $status = null) {
        $query = "SELECT * FROM {$this->table} WHERE employee_id = ? AND tenant_id = ?";
        $params = [$employeeId, $this->tenantId];

        if ($status) {
            $query .= " AND status = ?";
            $params[] = $status;
        }

        $query .= " ORDER BY start_date DESC";

        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll();
    }

    public function getPendingRequests() {
        $query = "SELECT lr.*, e.first_name, e.last_name, e.employee_code 
                  FROM {$this->table} lr
                  JOIN employees e ON lr.employee_id = e.id
                  WHERE lr.tenant_id = ? AND lr.status = 'pending'
                  ORDER BY lr.created_at DESC";
        
        $stmt = $this->execute($query, [$this->tenantId]);
        return $stmt->fetchAll();
    }

    public function updateStatus($requestId, $status, $approvedBy = null) {
        $query = "UPDATE {$this->table} SET status = ?, approved_by = ? WHERE id = ? AND tenant_id = ?";
        $this->execute($query, [$status, $approvedBy, $requestId, $this->tenantId]);
    }
}