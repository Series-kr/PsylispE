<?php
class CreateEmployeeSalaryComponentsTable {
    public function up($pdo) {
        $sql = "CREATE TABLE employee_salary_components (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            employee_id INT NOT NULL,
            component_id INT NOT NULL,
            custom_value DECIMAL(12,2) NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
            FOREIGN KEY (component_id) REFERENCES salary_components(id) ON DELETE CASCADE,
            UNIQUE KEY unique_employee_component (employee_id, component_id),
            INDEX idx_tenant_id (tenant_id),
            INDEX idx_employee_id (employee_id)
        )";
        $pdo->exec($sql);
    }
}