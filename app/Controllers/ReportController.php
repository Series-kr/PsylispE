<?php
namespace App\Controllers;

use App\Models\PayrollRun;
use App\Models\Employee;
use App\Models\AuditLog;

class ReportController {
    public function index() {
        session_start();
        $this->checkAuth();
        
        $tenantId = $_SESSION['tenant_id'];
        
        $this->renderView('reports/index', [
            'currentYear' => date('Y'),
            'currentMonth' => date('m')
        ]);
    }

    public function payrollSummary() {
        session_start();
        $this->checkAuth();

        $input = json_decode(file_get_contents('php://input'), true);
        $tenantId = $_SESSION['tenant_id'];
        $year = $input['year'] ?? date('Y');
        $month = $input['month'] ?? null;

        $payrollModel = new PayrollRun($tenantId);
        
        // Generate payroll summary report
        $reportData = $this->generatePayrollSummary($payrollModel, $year, $month);

        echo json_encode([
            'success' => true,
            'data' => $reportData
        ]);
    }

    public function taxReport() {
        session_start();
        $this->checkAuth();

        $input = json_decode(file_get_contents('php://input'), true);
        $tenantId = $_SESSION['tenant_id'];
        $year = $input['year'] ?? date('Y');

        // Generate tax report
        $reportData = $this->generateTaxReport($tenantId, $year);

        echo json_encode([
            'success' => true,
            'data' => $reportData
        ]);
    }

    public function employeeReport() {
        session_start();
        $this->checkAuth();

        $input = json_decode(file_get_contents('php://input'), true);
        $tenantId = $_SESSION['tenant_id'];
        $departmentId = $input['department_id'] ?? null;

        $employeeModel = new Employee($tenantId);
        $employees = $employeeModel->getByTenant($tenantId);

        // Filter by department if specified
        if ($departmentId) {
            $employees = array_filter($employees, function($emp) use ($departmentId) {
                return $emp['department_id'] == $departmentId;
            });
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'employees' => $employees,
                'total_count' => count($employees),
                'total_active' => count(array_filter($employees, function($emp) {
                    return $emp['is_active'];
                }))
            ]
        ]);
    }

    private function generatePayrollSummary($payrollModel, $year, $month = null) {
        $payrollRuns = $payrollModel->getByTenant($_SESSION['tenant_id']);
        
        $summary = [
            'total_payroll_runs' => 0,
            'total_gross' => 0,
            'total_net' => 0,
            'total_tax' => 0,
            'monthly_breakdown' => []
        ];

        foreach ($payrollRuns as $run) {
            $runYear = date('Y', strtotime($run['run_month']));
            $runMonth = date('m', strtotime($run['run_month']));

            if ($runYear == $year && (!$month || $runMonth == $month)) {
                $summary['total_payroll_runs']++;
                $summary['total_gross'] += $run['total_gross'];
                $summary['total_net'] += $run['total_net'];
                $summary['total_tax'] += ($run['total_gross'] - $run['total_net']);

                $monthKey = date('F Y', strtotime($run['run_month']));
                if (!isset($summary['monthly_breakdown'][$monthKey])) {
                    $summary['monthly_breakdown'][$monthKey] = [
                        'gross' => 0,
                        'net' => 0,
                        'tax' => 0,
                        'runs' => 0
                    ];
                }

                $summary['monthly_breakdown'][$monthKey]['gross'] += $run['total_gross'];
                $summary['monthly_breakdown'][$monthKey]['net'] += $run['total_net'];
                $summary['monthly_breakdown'][$monthKey]['tax'] += ($run['total_gross'] - $run['total_net']);
                $summary['monthly_breakdown'][$monthKey]['runs']++;
            }
        }

        return $summary;
    }

    private function generateTaxReport($tenantId, $year) {
        // This would generate detailed tax reports
        // For now, return sample structure
        return [
            'year' => $year,
            'total_tax_withheld' => 0,
            'employees_count' => 0,
            'tax_brackets' => []
        ];
    }

    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
    }

    private function renderView($view, $data = []) {
        extract($data);
        require __DIR__ . "/../../templates/$view.php";
    }
}