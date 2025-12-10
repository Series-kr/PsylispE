<?php
namespace App\Models;

class TaxRule extends BaseModel {
    protected $table = 'tax_rules';

    public function getByCountry($country, $date = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }

        $query = "SELECT * FROM {$this->table} 
                  WHERE (tenant_id = ? OR tenant_id IS NULL) 
                  AND country = ? 
                  AND is_active = 1 
                  AND effective_from <= ? 
                  AND (effective_to IS NULL OR effective_to >= ?)
                  ORDER BY min_amount ASC";
        
        $stmt = $this->execute($query, [$this->tenantId, $country, $date, $date]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        $this->execute($query, array_values($data));

        return $this->pdo->lastInsertId();
    }
}