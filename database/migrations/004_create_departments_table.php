<?php
class CreateDepartmentsTable {
    public function up($pdo) {
        $sql = "CREATE TABLE departments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            manager_id INT NULL,
            description TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            FOREIGN KEY (manager_id) REFERENCES employees(id) ON DELETE SET NULL,
            INDEX idx_tenant_id (tenant_id)
        )";
        $pdo->exec($sql);
    }
}