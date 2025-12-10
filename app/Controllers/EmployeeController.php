<?php
namespace App\Controllers;

use App\Models\Employee;
use App\Models\Department;

class EmployeeController {
    public function index() {
        session_start();
        $this->checkAuth();
        
        $tenantId = $_SESSION['tenant_id'];
        $employeeModel = new Employee($tenantId);
        $departmentModel = new Department($tenantId);

        $employees = $employeeModel->getByTenant($tenantId);
        $departments = $departmentModel->getAll();

        $this->renderView('employees/index', [
            'employees' => $employees,
            'departments' => $departments
        ]);
    }

    public function create() {
        session_start();
        $this->checkAuth();
        
        $tenantId = $_SESSION['tenant_id'];
        $departmentModel = new Department($tenantId);
        
        $this->renderView('employees/create', [
            'departments' => $departmentModel->getAll()
        ]);
    }

    public function store() {
        session_start();
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $tenantId = $_SESSION['tenant_id'];
        $employeeModel = new Employee($tenantId);

        try {
            $employeeId = $employeeModel->create([
                'tenant_id' => $tenantId,
                'employee_code' => $_POST['employee_code'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'] ?? null,
                'department_id' => $_POST['department_id'] ?? null,
                'job_title' => $_POST['job_title'] ?? null,
                'date_joined' => $_POST['date_joined'],
                'employment_type' => $_POST['employment_type'] ?? 'full_time',
                'bank_name' => $_POST['bank_name'] ?? null,
                'bank_account' => $_POST['bank_account'] ?? null,
                'tax_id' => $_POST['tax_id'] ?? null
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Employee created successfully',
                'employee_id' => $employeeId
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    public function import() {
        session_start();
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        $tenantId = $_SESSION['tenant_id'];
        $employeeModel = new Employee($tenantId);

        $file = $_FILES['file'];
        $filePath = __DIR__ . "/../../storage/uploads/" . uniqid() . '.csv';

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $result = $employeeModel->importFromCSV($tenantId, $filePath);
            
            // Clean up
            unlink($filePath);

            echo json_encode($result);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to process uploaded file']);
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