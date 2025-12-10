<?php
namespace App\Controllers;

use App\Models\PayrollRun;
use App\Models\Employee;
use App\Services\PayrollService;

class PayrollController {
    public function index() {
        session_start();
        $this->checkAuth();
        
        $tenantId = $_SESSION['tenant_id'];
        $payrollModel = new PayrollRun($tenantId);

        $payrollRuns = $payrollModel->getByTenant($tenantId);

        $this->renderView('payroll/index', [
            'payrollRuns' => $payrollRuns
        ]);
    }

    public function create() {
        session_start();
        $this->checkAuth();
        
        $tenantId = $_SESSION['tenant_id'];
        $employeeModel = new Employee($tenantId);

        $this->renderView('payroll/create', [
            'employees' => $employeeModel->getByTenant($tenantId),
            'currentMonth' => date('Y-m-01')
        ]);
    }

    public function calculate() {
        session_start();
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $tenantId = $_SESSION['tenant_id'];
        $runMonth = $input['run_month'] ?? date('Y-m-01');
        $employeeIds = $input['employee_ids'] ?? [];

        try {
            $payrollService = new PayrollService($tenantId);
            $result = $payrollService->calculatePayroll($runMonth, $employeeIds);

            echo json_encode([
                'success' => true,
                'message' => 'Payroll calculated successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    public function show($id) {
        session_start();
        $this->checkAuth();
        
        $tenantId = $_SESSION['tenant_id'];
        $payrollModel = new PayrollRun($tenantId);
        $payrollItemModel = new \App\Models\PayrollItem($tenantId);

        $payrollRun = $payrollModel->getById($id);
        $payrollItems = $payrollItemModel->getByPayrollRun($id);

        if (!$payrollRun || $payrollRun['tenant_id'] != $tenantId) {
            http_response_code(404);
            echo json_encode(['error' => 'Payroll run not found']);
            return;
        }

        $this->renderView('payroll/show', [
            'payrollRun' => $payrollRun,
            'payrollItems' => $payrollItems
        ]);
    }

    public function approve($id) {
        session_start();
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $tenantId = $_SESSION['tenant_id'];
        $userId = $_SESSION['user_id'];
        $payrollModel = new PayrollRun($tenantId);

        try {
            $payrollModel->updateStatus($id, 'approved', $userId);

            echo json_encode([
                'success' => true,
                'message' => 'Payroll approved successfully'
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'errors' => [$e->getMessage()]
            ]);
        }
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