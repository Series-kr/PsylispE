<?php
namespace App\Models;

class AuditLog extends BaseModel {
    protected $table = 'audit_logs';

    public function log($data) {
        $query = "INSERT INTO {$this->table} (tenant_id, user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->execute($query, [
            $data['tenant_id'] ?? null,
            $data['user_id'],
            $data['action'],
            $data['table_name'] ?? null,
            $data['record_id'] ?? null,
            $data['old_values'] ? json_encode($data['old_values']) : null,
            $data['new_values'] ? json_encode($data['new_values']) : null,
            $data['ip_address'] ?? $_SERVER['REMOTE_ADDR'] ?? null,
            $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        return $this->pdo->lastInsertId();
    }

    public function getLogs($filters = []) {
        $query = "SELECT al.*, u.first_name, u.last_name, u.email 
                  FROM {$this->table} al
                  JOIN users u ON al.user_id = u.id
                  WHERE al.tenant_id = ?";
        
        $params = [$this->tenantId];

        if (!empty($filters['action'])) {
            $query .= " AND al.action = ?";
            $params[] = $filters['action'];
        }

        if (!empty($filters['user_id'])) {
            $query .= " AND al.user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['start_date'])) {
            $query .= " AND DATE(al.created_at) >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $query .= " AND DATE(al.created_at) <= ?";
            $params[] = $filters['end_date'];
        }

        $query .= " ORDER BY al.created_at DESC LIMIT 1000";

        $stmt = $this->execute($query, $params);
        $logs = $stmt->fetchAll();

        // Decode JSON values
        foreach ($logs as &$log) {
            if ($log['old_values']) {
                $log['old_values'] = json_decode($log['old_values'], true);
            }
            if ($log['new_values']) {
                $log['new_values'] = json_decode($log['new_values'], true);
            }
        }

        return $logs;
    }
}