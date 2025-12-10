<?php
namespace App\Controllers;

use App\Models\Employee;
use App\Models\PayrollRun;

class DashboardController {
    public function index() {
        session_start();
        $this->checkAuth();

        $tenantId = $_SESSION['tenant_id'];
        $userRole = $_SESSION['user_role'];

        if ($userRole === 'employee') {
            $this->employeeDashboard($tenantId, $_SESSION['user_id']);
            return;
        }

        $employeeModel = new Employee($tenantId);
        $payrollModel = new PayrollRun($tenantId);

        $data = [
            'totalEmployees' => count($employeeModel->getByTenant($tenantId)),
            'activePayrolls' => $payrollModel->getByTenant($tenantId, 'draft'),
            'recentPayrolls' => array_slice($payrollModel->getByTenant($tenantId), 0, 5)
        ];

        $this->renderView('dashboard/admin', $data);
    }

    private function employeeDashboard($tenantId, $userId) {
        // Employee-specific dashboard
        $this->renderView('dashboard/employee');
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