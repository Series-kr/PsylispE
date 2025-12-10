<?php
class CreateCompaniesTable {
    public function up($pdo) {
        $sql = "CREATE TABLE companies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE,
            address TEXT,
            country VARCHAR(100) DEFAULT 'US',
            currency CHAR(3) DEFAULT 'USD',
            timezone VARCHAR(50) DEFAULT 'UTC',
            logo VARCHAR(255) NULL,
            plan ENUM('trial','basic','premium') DEFAULT 'trial',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            INDEX idx_slug (slug)
        )";
        $pdo->exec($sql);
    }
}