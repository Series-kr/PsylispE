<?php
namespace App\Config;

class Routes {
    public static function getRoutes() {
        return [
            'GET' => [
                '/' => 'DashboardController@index',
                '/dashboard' => 'DashboardController@index',
                '/employees' => 'EmployeeController@index',
                '/employees/create' => 'EmployeeController@create',
                '/employees/{id}' => 'EmployeeController@show',
                '/employees/{id}/edit' => 'EmployeeController@edit',
                '/payroll' => 'PayrollController@index',
                '/payroll/create' => 'PayrollController@create',
                '/payroll/{id}' => 'PayrollController@show',
                '/payslips' => 'PayslipController@index',
                '/payslip/download/{id}' => 'PayslipController@download',
                '/reports' => 'ReportController@index',
                '/settings' => 'SettingsController@index',
                '/company/settings' => 'CompanyController@settings',
                '/company/onboarding' => 'CompanyController@onboarding',
                '/auth/login' => 'AuthController@login',
                '/auth/logout' => 'AuthController@logout',
                '/auth/forgot-password' => 'AuthController@forgotPassword',
                '/auth/reset-password' => 'AuthController@resetPassword'
            ],
            'POST' => [
                '/auth/login' => 'AuthController@login',
                '/auth/forgot-password' => 'AuthController@forgotPassword',
                '/auth/reset-password' => 'AuthController@resetPassword',
                '/employees' => 'EmployeeController@store',
                '/employees/import' => 'EmployeeController@import',
                '/employees/{id}' => 'EmployeeController@update',
                '/employees/{id}/delete' => 'EmployeeController@delete',
                '/payroll/calculate' => 'PayrollController@calculate',
                '/payroll/{id}/approve' => 'PayrollController@approve',
                '/payroll/{id}/process' => 'PayrollController@process',
                '/payslip/generate/{id}' => 'PayslipController@generate',
                '/payslip/email/{id}' => 'PayslipController@email',
                '/reports/payroll-summary' => 'ReportController@payrollSummary',
                '/reports/tax-report' => 'ReportController@taxReport',
                '/reports/employee-report' => 'ReportController@employeeReport',
                '/settings/salary-components' => 'SettingsController@salaryComponents',
                '/settings/tax-settings' => 'SettingsController@taxSettings',
                '/settings/user-profile' => 'SettingsController@userProfile',
                '/company/settings/update' => 'CompanyController@updateSettings',
                '/company/onboarding/complete' => 'CompanyController@completeOnboarding'
            ],
            'PUT' => [
                '/employees/{id}' => 'EmployeeController@update',
                '/settings/salary-components/{id}' => 'SettingsController@updateSalaryComponent'
            ],
            'DELETE' => [
                '/employees/{id}' => 'EmployeeController@delete',
                '/settings/salary-components/{id}' => 'SettingsController@deleteSalaryComponent'
            ]
        ];
    }

    public static function getApiRoutes() {
        return [
            'GET' => [
                '/api/employees' => 'EmployeeController@apiIndex',
                '/api/employees/{id}' => 'EmployeeController@apiShow',
                '/api/payroll/runs' => 'PayrollController@apiIndex',
                '/api/payroll/runs/{id}' => 'PayrollController@apiShow',
                '/api/payroll/items/{id}/breakdown' => 'PayrollController@apiBreakdown',
                '/api/reports/payroll-summary' => 'ReportController@apiPayrollSummary',
                '/api/reports/tax-report' => 'ReportController@apiTaxReport'
            ],
            'POST' => [
                '/api/auth/login' => 'AuthController@apiLogin',
                '/api/employees' => 'EmployeeController@apiStore',
                '/api/employees/import' => 'EmployeeController@apiImport',
                '/api/payroll/calculate' => 'PayrollController@apiCalculate',
                '/api/payroll/runs/{id}/approve' => 'PayrollController@apiApprove',
                '/api/payslip/generate' => 'PayslipController@apiGenerate'
            ],
            'PUT' => [
                '/api/employees/{id}' => 'EmployeeController@apiUpdate'
            ],
            'DELETE' => [
                '/api/employees/{id}' => 'EmployeeController@apiDelete'
            ]
        ];
    }

    public static function getMiddleware($route) {
        $protectedRoutes = [
            'auth' => [
                '/dashboard',
                '/employees',
                '/payroll',
                '/payslips',
                '/reports',
                '/settings',
                '/company/settings'
            ],
            'company_admin' => [
                '/company/settings',
                '/company/onboarding/complete',
                '/settings/tax-settings'
            ],
            'hr' => [
                '/employees',
                '/employees/create',
                '/employees/import'
            ],
            'payroll' => [
                '/payroll',
                '/payroll/create',
                '/payroll/calculate',
                '/payroll/{id}/approve'
            ]
        ];

        foreach ($protectedRoutes as $middleware => $routes) {
            foreach ($routes as $protectedRoute) {
                if (self::routeMatches($route, $protectedRoute)) {
                    return $middleware;
                }
            }
        }

        return null;
    }

    private static function routeMatches($requestRoute, $definedRoute) {
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $definedRoute);
        $pattern = "#^$pattern$#";
        return preg_match($pattern, $requestRoute);
    }

    public static function getRouteParameters($requestRoute, $definedRoute) {
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $definedRoute);
        $pattern = "#^$pattern$#";
        
        if (preg_match($pattern, $requestRoute, $matches)) {
            array_shift($matches); // Remove full match
            return $matches;
        }
        
        return [];
    }
}