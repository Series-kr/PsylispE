<?php
class CreateAuditLogsTable {
    public function up($pdo) {
        $sql = "CREATE TABLE audit_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tenant_id INT NULL,
            user_id INT NOT NULL,
            action VARCHAR(255) NOT NULL,
            table_name VARCHAR(100) NULL,
            record_id INT NULL,
            old_values JSON NULL,
            new_values JSON NULL,
            ip_address VARCHAR(45) NULL,
            user_agent VARCHAR(500) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tenant_id) REFERENCES companies(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_tenant_id (tenant_id),
            INDEX idx_user_id (user_id),
            INDEX idx_action (action),
            INDEX idx_created_at (created_at)
        )";
        $pdo->exec($sql);
    }
}