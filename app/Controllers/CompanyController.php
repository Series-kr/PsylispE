<?php
namespace App\Controllers;

use App\Models\Company;
use App\Models\User;

class CompanyController {
    public function settings() {
        session_start();
        $this->checkAuth();
        
        $tenantId = $_SESSION['tenant_id'];
        $companyModel = new Company();
        $company = $companyModel->getById($tenantId);

        $this->renderView('settings/company', [
            'company' => $company
        ]);
    }

    public function updateSettings() {
        session_start();
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $tenantId = $_SESSION['tenant_id'];
        $companyModel = new Company();

        try {
            $data = [
                'name' => $_POST['name'] ?? null,
                'address' => $_POST['address'] ?? null,
                'country' => $_POST['country'] ?? null,
                'currency' => $_POST['currency'] ?? null,
                'timezone' => $_POST['timezone'] ?? null
            ];

            // Remove null values
            $data = array_filter($data);

            $companyModel->update($tenantId, $data);

            echo json_encode([
                'success' => true,
                'message' => 'Company settings updated successfully'
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    public function onboarding() {
        // Company onboarding wizard
        session_start();
        
        if (isset($_SESSION['user_id']) && $_SESSION['tenant_id']) {
            header('Location: /dashboard');
            exit;
        }

        $this->renderView('company/onboarding');
    }

    public function completeOnboarding() {
        session_start();
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        try {
            $companyModel = new Company();
            $userModel = new User();

            // Update company settings
            $companyData = [
                'name' => $_POST['company_name'],
                'country' => $_POST['country'],
                'currency' => $_POST['currency'],
                'timezone' => $_POST['timezone'],
                'plan' => 'trial'
            ];

            $companyModel->update($_SESSION['tenant_id'], $companyData);

            echo json_encode([
                'success' => true,
                'message' => 'Onboarding completed successfully',
                'redirect' => '/dashboard'
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