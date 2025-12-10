<?php
namespace App\Models;

class Loan extends BaseModel {
    protected $table = 'loans';

    public function createLoan($data) {
        $query = "INSERT INTO {$this->table} (tenant_id, employee_id, principal_amount, outstanding_balance, monthly_deduction, interest_rate, start_date, end_date, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->execute($query, [
            $data['tenant_id'],
            $data['employee_id'],
            $data['principal_amount'],
            $data['principal_amount'], // Start with full amount outstanding
            $data['monthly_deduction'],
            $data['interest_rate'] ?? 0,
            $data['start_date'],
            $data['end_date'],
            $data['status'] ?? 'active'
        ]);

        return $this->pdo->lastInsertId();
    }

    public function getActiveLoans($employeeId = null) {
        $query = "SELECT l.*, e.first_name, e.last_name, e.employee_code 
                  FROM {$this->table} l
                  JOIN employees e ON l.employee_id = e.id
                  WHERE l.tenant_id = ? AND l.status = 'active'";
        
        $params = [$this->tenantId];
        
        if ($employeeId) {
            $query .= " AND l.employee_id = ?";
            $params[] = $employeeId;
        }

        $query .= " ORDER BY l.start_date DESC";

        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll();
    }

    public function processDeduction($loanId, $amount) {
        $query = "UPDATE {$this->table} SET outstanding_balance = outstanding_balance - ? 
                  WHERE id = ? AND tenant_id = ?";
        
        $this->execute($query, [$amount, $loanId, $this->tenantId]);

        // Check if loan is fully paid
        $checkQuery = "SELECT outstanding_balance FROM {$this->table} WHERE id = ?";
        $stmt = $this->execute($checkQuery, [$loanId]);
        $loan = $stmt->fetch();

        if ($loan['outstanding_balance'] <= 0) {
            $this->execute("UPDATE {$this->table} SET status = 'paid' WHERE id = ?", [$loanId]);
        }
    }

    public function getLoanHistory($employeeId) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE employee_id = ? AND tenant_id = ?
                  ORDER BY start_date DESC";
        
        $stmt = $this->execute($query, [$employeeId, $this->tenantId]);
        return $stmt->fetchAll();
    }
}