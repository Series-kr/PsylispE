<?php
class CreateLeaveRequestsTable {
    public function up($pdo) {
        $sql = "CREATE TABLE leave_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            employee_id INT NOT NULL,
            leave_type ENUM('sick','vacation','personal','maternity','paternity','other') NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            reason TEXT NULL,
            status ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
            approved_by INT NULL,
            approved_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
            FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_tenant_id (tenant_id),
            INDEX idx_employee_id (employee_id),
            INDEX idx_status (status),
            INDEX idx_dates (start_date, end_date)
        )";
        $pdo->exec($sql);
    }
}