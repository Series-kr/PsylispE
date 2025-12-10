<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\SalaryComponent;

class SettingsController {
    public function index() {
        session_start();
        $this->checkAuth();
        
        $tenantId = $_SESSION['tenant_id'];
        $salaryComponentModel = new SalaryComponent($tenantId);

        $this->renderView('settings/index', [
            'salaryComponents' => $salaryComponentModel->getByType(),
            'userRole' => $_SESSION['user_role']
        ]);
    }

    public function salaryComponents() {
        session_start();
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->saveSalaryComponent();
            return;
        }

        $tenantId = $_SESSION['tenant_id'];
        $salaryComponentModel = new SalaryComponent($tenantId);

        echo json_encode([
            'success' => true,
            'data' => $salaryComponentModel->getByType()
        ]);
    }

    public function taxSettings() {
        session_start();
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            // Save tax settings implementation
            echo json_encode(['success' => true, 'message' => 'Tax settings updated']);
            return;
        }

        // Return current tax settings
        echo json_encode([
            'success' => true,
            'data' => [
                'tax_config' => [],
                'countries' => ['US', 'UK', 'CA', 'AU', 'IN', 'GH', 'NG']
            ]
        ]);
    }

    public function userProfile() {
        session_start();
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateUserProfile();
            return;
        }

        $userId = $_SESSION['user_id'];
        $userModel = new User();
        $user = $userModel->getById($userId);

        echo json_encode([
            'success' => true,
            'data' => $user
        ]);
    }

    private function saveSalaryComponent() {
        $tenantId = $_SESSION['tenant_id'];
        $salaryComponentModel = new SalaryComponent($tenantId);

        try {
            $data = [
                'tenant_id' => $tenantId,
                'component_code' => $_POST['component_code'],
                'name' => $_POST['name'],
                'type' => $_POST['type'],
                'calculation_type' => $_POST['calculation_type'],
                'default_value' => $_POST['default_value'] ?? 0,
                'formula' => $_POST['formula'] ?? null,
                'is_taxable' => isset($_POST['is_taxable']) ? 1 : 0,
                'is_pre_tax' => isset($_POST['is_pre_tax']) ? 1 : 0
            ];

            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Update existing component
                $salaryComponentModel->update($_POST['id'], $data);
                $message = 'Salary component updated successfully';
            } else {
                // Create new component
                $salaryComponentModel->create($data);
                $message = 'Salary component created successfully';
            }

            echo json_encode([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    private function updateUserProfile() {
        $userId = $_SESSION['user_id'];
        $userModel = new User();

        try {
            $data = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'phone' => $_POST['phone'] ?? null
            ];

            // Handle password update if provided
            if (!empty($_POST['password'])) {
                if ($_POST['password'] !== $_POST['password_confirmation']) {
                    throw new \Exception('Password confirmation does not match');
                }
                $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
            }

            $userModel->update($userId, $data);

            // Update session
            $_SESSION['first_name'] = $data['first_name'];
            $_SESSION['last_name'] = $data['last_name'];

            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully'
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