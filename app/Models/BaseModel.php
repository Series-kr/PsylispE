<?php
namespace App\Models;

use App\Config\Database;
use App\Helpers\Encryption;

abstract class BaseModel {
    protected $pdo;
    protected $table;
    protected $tenantId;

    public function __construct($tenantId = null) {
        $this->pdo = Database::getInstance();
        $this->tenantId = $tenantId;
    }

    protected function scopeTenant($query) {
        if ($this->tenantId && !in_array($this->table, ['companies', 'users'])) {
            return $query . " WHERE tenant_id = ?";
        }
        return $query;
    }

    protected function execute($query, $params = []) {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    protected function encryptFields($data) {
        $encryptedFields = ['bank_account', 'tax_id'];
        foreach ($encryptedFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = Encryption::encrypt($data[$field]);
            }
        }
        return $data;
    }

    protected function decryptFields($data) {
        $encryptedFields = ['bank_account', 'tax_id'];
        foreach ($encryptedFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = Encryption::decrypt($data[$field]);
            }
        }
        return $data;
    }
}