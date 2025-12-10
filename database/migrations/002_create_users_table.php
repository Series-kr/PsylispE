<?php
class CreateUsersTable {
    public function up($pdo) {
        $sql = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('super_admin','company_admin','hr','payroll','employee') NOT NULL,
            phone VARCHAR(20) NULL,
            is_active TINYINT(1) DEFAULT 1,
            two_factor_enabled TINYINT(1) DEFAULT 0,
            two_factor_secret VARCHAR(255) NULL,
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            INDEX idx_tenant_id (tenant_id),
            INDEX idx_email (email)
        )";
        $pdo->exec($sql);
    }
}