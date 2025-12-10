<?php
class CreateEmployeesTable {
    public function up($pdo) {
        $sql = "CREATE TABLE employees (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            user_id INT NULL,
            employee_code VARCHAR(50) NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            dob DATE NULL,
            email VARCHAR(150) NOT NULL,
            phone VARCHAR(20) NULL,
            department_id INT NULL,
            job_title VARCHAR(150) NULL,
            date_joined DATE NOT NULL,
            employment_type ENUM('full_time','part_time','contract','temporary') DEFAULT 'full_time',
            bank_name VARCHAR(100) NULL,
            bank_account VARCHAR(255) NULL,
            tax_id VARCHAR(255) NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            UNIQUE KEY unique_employee_code (tenant_id, employee_code),
            INDEX idx_tenant_id (tenant_id),
            INDEX idx_employee_code (employee_code)
        )";
        $pdo->exec($sql);
    }
}