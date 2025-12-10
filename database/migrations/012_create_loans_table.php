<?php
class CreateLoansTable {
    public function up($pdo) {
        $sql = "CREATE TABLE loans (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            employee_id INT NOT NULL,
            principal_amount DECIMAL(12,2) NOT NULL,
            outstanding_balance DECIMAL(12,2) NOT NULL,
            monthly_deduction DECIMAL(12,2) NOT NULL,
            interest_rate DECIMAL(5,2) DEFAULT 0.00,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            status ENUM('active','paid','cancelled') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
            INDEX idx_tenant_id (tenant_id),
            INDEX idx_employee_id (employee_id),
            INDEX idx_status (status)
        )";
        $pdo->exec($sql);
    }
}