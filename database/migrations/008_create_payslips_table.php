<?php
class CreatePayslipsTable {
    public function up($pdo) {
        $sql = "CREATE TABLE payslips (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            payroll_item_id INT NOT NULL,
            pdf_path VARCHAR(255) NOT NULL,
            emailed TINYINT(1) DEFAULT 0,
            emailed_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            FOREIGN KEY (payroll_item_id) REFERENCES payroll_items(id) ON DELETE CASCADE,
            INDEX idx_tenant_id (tenant_id),
            INDEX idx_payroll_item_id (payroll_item_id)
        )";
        $pdo->exec($sql);
    }
}