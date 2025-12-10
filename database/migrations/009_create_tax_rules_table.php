<?php
class CreateTaxRulesTable {
    public function up($pdo) {
        $sql = "CREATE TABLE tax_rules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NULL,
            country VARCHAR(100) NOT NULL,
            rule_name VARCHAR(255) NOT NULL,
            min_amount DECIMAL(12,2) DEFAULT 0.00,
            max_amount DECIMAL(12,2) DEFAULT 999999999.99,
            rate DECIMAL(5,2) NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            effective_from DATE NOT NULL,
            effective_to DATE NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            INDEX idx_tenant_country (tenant_id, country),
            INDEX idx_country (country)
        )";
        $pdo->exec($sql);
    }
}