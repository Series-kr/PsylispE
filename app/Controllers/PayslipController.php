<?php
namespace App\Controllers;

use App\Models\Payslip;
use App\Models\PayrollItem;

class PayslipController {
    public function index() {
        session_start();
        $this->checkAuth();
        
        $tenantId = $_SESSION['tenant_id'];
        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];

        $payslipModel = new Payslip($tenantId);

        if ($userRole === 'employee') {
            $payslips = $payslipModel->getByEmployee($userId);
        } else {
            $payslips = $payslipModel->getByTenant($tenantId);
        }

        $this->renderView('payslips/index', [
            'payslips' => $payslips
        ]);
    }

    public function generate($payrollItemId) {
        session_start();
        $this->checkAuth();

        $tenantId = $_SESSION['tenant_id'];
        $payslipModel = new Payslip($tenantId);
        $payrollItemModel = new PayrollItem($tenantId);

        try {
            $payrollItem = $payrollItemModel->getById($payrollItemId);
            
            if (!$payrollItem || $payrollItem['tenant_id'] != $tenantId) {
                throw new \Exception('Payroll item not found');
            }

            $pdfPath = $payslipModel->generatePDF($payrollItem);
            
            $payslipId = $payslipModel->create([
                'tenant_id' => $tenantId,
                'payroll_item_id' => $payrollItemId,
                'pdf_path' => $pdfPath
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Payslip generated successfully',
                'payslip_id' => $payslipId,
                'pdf_url' => '/payslip/download/' . $payslipId
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    public function download($payslipId) {
        session_start();
        $this->checkAuth();

        $tenantId = $_SESSION['tenant_id'];
        $payslipModel = new Payslip($tenantId);

        $payslip = $payslipModel->getById($payslipId);
        
        if (!$payslip || $payslip['tenant_id'] != $tenantId) {
            http_response_code(404);
            echo 'Payslip not found';
            return;
        }

        $filePath = __DIR__ . '/../../storage/' . $payslip['pdf_path'];
        
        if (file_exists($filePath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="payslip-' . $payslipId . '.pdf"');
            readfile($filePath);
        } else {
            http_response_code(404);
            echo 'PDF file not found';
        }
    }

    public function email($payslipId) {
        session_start();
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $tenantId = $_SESSION['tenant_id'];
        $payslipModel = new Payslip($tenantId);

        try {
            $payslipModel->sendEmail($payslipId);
            
            echo json_encode([
                'success' => true,
                'message' => 'Payslip emailed successfully'
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