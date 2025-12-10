<?php
class CreatePayrollRunsTable {
    public function up($pdo) {
        $sql = "CREATE TABLE payroll_runs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            run_month DATE NOT NULL,
            status ENUM('draft','pending_approval','approved','paid','reversed') DEFAULT 'draft',
            total_gross DECIMAL(14,2) DEFAULT 0.00,
            total_net DECIMAL(14,2) DEFAULT 0.00,
            run_by INT NOT NULL,
            approved_by INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            FOREIGN KEY (run_by) REFERENCES users(id),
            FOREIGN KEY (approved_by) REFERENCES users(id),
            UNIQUE KEY unique_payroll_run (tenant_id, run_month),
            INDEX idx_tenant_id (tenant_id),
            INDEX idx_run_month (run_month)
        )";
        $pdo->exec($sql);
    }
}