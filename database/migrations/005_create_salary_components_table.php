<?php
class CreateSalaryComponentsTable {
    public function up($pdo) {
        $sql = "CREATE TABLE salary_components (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            component_code VARCHAR(50) NOT NULL,
            name VARCHAR(100) NOT NULL,
            type ENUM('earning','deduction') NOT NULL,
            calculation_type ENUM('fixed','percentage','formula') NOT NULL,
            default_value DECIMAL(12,2) DEFAULT 0.00,
            formula TEXT NULL,
            is_taxable TINYINT(1) DEFAULT 0,
            is_pre_tax TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            UNIQUE KEY unique_component_code (tenant_id, component_code),
            INDEX idx_tenant_id (tenant_id)
        )";
        $pdo->exec($sql);
    }
}