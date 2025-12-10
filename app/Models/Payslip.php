<?php
namespace App\Models;

class Payslip extends BaseModel {
    protected $table = 'payslips';

    public function generatePDF($payrollItem) {
        // Simple PDF generation - in production, use Dompdf/TCPDF
        $pdfContent = $this->generatePDFContent($payrollItem);
        
        $filename = 'payslips/payslip-' . $payrollItem['id'] . '-' . time() . '.pdf';
        $filePath = __DIR__ . '/../../storage/' . $filename;
        
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // For now, we'll create a simple text file as PDF
        // In production, integrate with Dompdf or TCPDF
        file_put_contents($filePath, $pdfContent);
        
        return $filename;
    }

    private function generatePDFContent($payrollItem) {
        $details = json_decode($payrollItem['details'], true);
        
        $content = "PAYSLIP\n";
        $content .= "========\n\n";
        $content .= "Employee: {$payrollItem['first_name']} {$payrollItem['last_name']}\n";
        $content .= "Employee Code: {$payrollItem['employee_code']}\n";
        $content .= "Job Title: {$payrollItem['job_title']}\n\n";
        
        $content .= "EARNINGS:\n";
        $content .= "---------\n";
        foreach ($details['earnings'] as $earning) {
            $content .= sprintf("%-20s: %10.2f\n", $earning['component'], $earning['amount']);
        }
        $content .= sprintf("%-20s: %10.2f\n", "Total Gross", $payrollItem['gross']);
        
        $content .= "\nDEDUCTIONS:\n";
        $content .= "-----------\n";
        foreach ($details['deductions'] as $deduction) {
            $content .= sprintf("%-20s: %10.2f\n", $deduction['component'], $deduction['amount']);
        }
        $content .= sprintf("%-20s: %10.2f\n", "Total Deductions", $payrollItem['total_deductions']);
        
        $content .= "\n";
        $content .= sprintf("%-20s: %10.2f\n", "NET PAY", $payrollItem['net_pay']);
        
        $content .= "\n\nGenerated on: " . date('Y-m-d H:i:s');
        
        return $content;
    }

    public function getByEmployee($employeeId) {
        $query = "SELECT p.*, pr.run_month, pi.net_pay 
                  FROM {$this->table} p
                  JOIN payroll_items pi ON p.payroll_item_id = pi.id
                  JOIN payroll_runs pr ON pi.payroll_run_id = pr.id
                  WHERE pi.employee_id = ? AND p.tenant_id = ?
                  ORDER BY pr.run_month DESC";
        
        $stmt = $this->execute($query, [$employeeId, $this->tenantId]);
        return $stmt->fetchAll();
    }

    public function getByTenant($tenantId) {
        $query = "SELECT p.*, pr.run_month, pi.net_pay, e.first_name, e.last_name, e.employee_code
                  FROM {$this->table} p
                  JOIN payroll_items pi ON p.payroll_item_id = pi.id
                  JOIN payroll_runs pr ON pi.payroll_run_id = pr.id
                  JOIN employees e ON pi.employee_id = e.id
                  WHERE p.tenant_id = ?
                  ORDER BY pr.run_month DESC, e.first_name, e.last_name";
        
        $stmt = $this->execute($query, [$tenantId]);
        return $stmt->fetchAll();
    }

    public function sendEmail($payslipId) {
        // Implement email sending logic
        // This would integrate with your email service
        $payslip = $this->getById($payslipId);
        
        // Update emailed status
        $query = "UPDATE {$this->table} SET emailed = 1, emailed_at = NOW() WHERE id = ?";
        $this->execute($query, [$payslipId]);
        
        return true;
    }

    public function getById($id) {
        $query = "SELECT p.*, pi.employee_id, pi.payroll_run_id 
                  FROM {$this->table} p
                  JOIN payroll_items pi ON p.payroll_item_id = pi.id
                  WHERE p.id = ? AND p.tenant_id = ?";
        
        $stmt = $this->execute($query, [$id, $this->tenantId]);
        return $stmt->fetch();
    }
}