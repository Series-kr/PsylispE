<?php
namespace App\Models;

class PayrollItem extends BaseModel {
    protected $table = 'payroll_items';

    public function create($data) {
        $query = "INSERT INTO {$this->table} (tenant_id, payroll_run_id, employee_id, gross, total_deductions, net_pay, details) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->execute($query, [
            $data['tenant_id'],
            $data['payroll_run_id'],
            $data['employee_id'],
            $data['gross'],
            $data['total_deductions'],
            $data['net_pay'],
            json_encode($data['details'])
        ]);
        return $this->pdo->lastInsertId();
    }

    public function getByPayrollRun($payrollRunId) {
        $query = "SELECT pi.*, e.first_name, e.last_name, e.employee_code, e.job_title
                  FROM {$this->table} pi
                  JOIN employees e ON pi.employee_id = e.id
                  WHERE pi.payroll_run_id = ?";
        
        $stmt = $this->execute($query, [$payrollRunId]);
        $items = $stmt->fetchAll();

        // Decode JSON details
        foreach ($items as &$item) {
            $item['details'] = json_decode($item['details'], true);
        }

        return $items;
    }
}