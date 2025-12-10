<?php
class CreateEmployeeSalaryTable {
    public function up($pdo) {
        $sql = "CREATE TABLE employee_salary (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            employee_id INT NOT NULL,
            salary_grade_id INT NULL,
            base_salary DECIMAL(12,2) NOT NULL,
            effective_from DATE NOT NULL,
            effective_to DATE NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
            INDEX idx_tenant_id (tenant_id),
            INDEX idx_employee_id (employee_id),
            INDEX idx_effective_dates (effective_from, effective_to)
        )";
        $pdo->exec($sql);
    }
}