<?php
namespace App\Middleware;

class AuthMiddleware {
    public function handle($request, $next) {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            if ($this->isAjaxRequest()) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            } else {
                header('Location: /auth/login');
                exit;
            }
        }

        return $next($request);
    }

    public function requireRole($roles) {
        return function($request, $next) use ($roles) {
            if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], (array)$roles)) {
                http_response_code(403);
                echo json_encode(['error' => 'Forbidden']);
                exit;
            }
            return $next($request);
        };
    }

    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}