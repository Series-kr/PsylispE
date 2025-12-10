<?php
require_once __DIR__ . '/../app/Config/Env.php';
require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Config/Routes.php';

// Load environment
App\Config\Env::load(__DIR__ . '/../.env');

// Error reporting
if ($_ENV['APP_ENV'] === 'local') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session configuration
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

// Handle the request
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Check if it's an API request
$isApiRequest = strpos($requestUri, '/api/') === 0;
$routes = $isApiRequest 
    ? App\Config\Routes::getApiRoutes() 
    : App\Config\Routes::getRoutes();

try {
    $matched = false;
    
    foreach ($routes[$method] as $route => $handler) {
        $params = App\Config\Routes::getRouteParameters($requestUri, $route);
        
        if (!empty($params) || $requestUri === $route) {
            // Check middleware
            $middleware = App\Config\Routes::getMiddleware($route);
            if ($middleware && !self::checkMiddleware($middleware, $isApiRequest)) {
                self::handleUnauthorized($isApiRequest);
                return;
            }
            
            list($controller, $action) = explode('@', $handler);
            $controllerClass = "App\\Controllers\\$controller";
            
            // Load controller file
            $controllerFile = __DIR__ . "/../app/Controllers/$controller.php";
            if (!file_exists($controllerFile)) {
                throw new Exception("Controller $controller not found");
            }
            
            require_once $controllerFile;
            
            if (!class_exists($controllerClass)) {
                throw new Exception("Controller class $controllerClass not found");
            }
            
            $controllerInstance = new $controllerClass();
            
            if (!method_exists($controllerInstance, $action)) {
                throw new Exception("Method $action not found in $controllerClass");
            }
            
            call_user_func_array([$controllerInstance, $action], $params);
            
            $matched = true;
            break;
        }
    }

    if (!$matched) {
        self::handleNotFound($isApiRequest);
    }
} catch (Exception $e) {
    error_log("Application error: " . $e->getMessage());
    self::handleError($e, $isApiRequest);
}

function checkMiddleware($middleware, $isApiRequest) {
    switch ($middleware) {
        case 'auth':
            return isset($_SESSION['user_id']);
        case 'company_admin':
            return isset($_SESSION['user_id']) && in_array($_SESSION['user_role'], ['company_admin', 'super_admin']);
        case 'hr':
            return isset($_SESSION['user_id']) && in_array($_SESSION['user_role'], ['hr', 'company_admin', 'super_admin']);
        case 'payroll':
            return isset($_SESSION['user_id']) && in_array($_SESSION['user_role'], ['payroll', 'company_admin', 'super_admin']);
        default:
            return true;
    }
}

function handleUnauthorized($isApiRequest) {
    if ($isApiRequest) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized']);
    } else {
        header('Location: /auth/login');
    }
    exit;
}

function handleNotFound($isApiRequest) {
    if ($isApiRequest) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Endpoint not found']);
    } else {
        http_response_code(404);
        echo "404 - Page not found";
    }
    exit;
}

function handleError($exception, $isApiRequest) {
    if ($isApiRequest) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Internal server error']);
    } else {
        http_response_code(500);
        echo "500 - Internal server error";
        if ($_ENV['APP_ENV'] === 'local') {
            echo ": " . $exception->getMessage();
        }
    }
    exit;
}