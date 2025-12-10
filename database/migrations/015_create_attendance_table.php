<?php
class CreateAttendanceTable {
    public function up($pdo) {
        $sql = "CREATE TABLE attendance (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NOT NULL,
            employee_id INT NOT NULL,
            date DATE NOT NULL,
            check_in TIME NULL,
            check_out TIME NULL,
            working_hours DECIMAL(4,2) DEFAULT 0.00,
            status ENUM('present','absent','late','half_day','holiday') DEFAULT 'present',
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
            UNIQUE KEY unique_attendance (tenant_id, employee_id, date),
            INDEX idx_tenant_id (tenant_id),
            INDEX idx_employee_id (employee_id),
            INDEX idx_date (date)
        )";
        $pdo->exec($sql);
    }
}