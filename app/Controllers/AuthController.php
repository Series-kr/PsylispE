<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if ($user && $userModel->verifyPassword($password, $user['password']) && $user['is_active']) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['tenant_id'] = $user['tenant_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['first_name'] = $user['first_name'];

                // Update last login
                $userModel->updateLastLogin($user['id']);

                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => $this->getRedirectUrl($user['role'])
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'errors' => ['Invalid credentials or inactive account']
                ]);
            }
        } else {
            // Show login form
            $this->renderView('auth/login');
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /auth/login');
        exit;
    }

    private function getRedirectUrl($role) {
        switch ($role) {
            case 'super_admin':
                return '/admin/dashboard';
            case 'company_admin':
            case 'hr':
            case 'payroll':
                return '/dashboard';
            case 'employee':
                return '/employee/dashboard';
            default:
                return '/dashboard';
        }
    }

    private function renderView($view, $data = []) {
        extract($data);
        require __DIR__ . "/../../templates/$view.php";
    }
}