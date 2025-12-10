<?php
class CreatePayrollItemsTable {
    public function up($pdo) {
        $sql = "CREATE TABLE payroll_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            payroll_run_id INT NOT NULL,
            employee_id INT NOT NULL,
            gross DECIMAL(12,2) DEFAULT 0.00,
            total_deductions DECIMAL(12,2) DEFAULT 0.00,
            net_pay DECIMAL(12,2) DEFAULT 0.00,
            details JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            FOREIGN KEY (payroll_run_id) REFERENCES payroll_runs(id) ON DELETE CASCADE,
            FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
            UNIQUE KEY unique_payroll_item (payroll_run_id, employee_id),
            INDEX idx_tenant_id (tenant_id),
            INDEX idx_payroll_run_id (payroll_run_id)
        )";
        $pdo->exec($sql);
    }
}