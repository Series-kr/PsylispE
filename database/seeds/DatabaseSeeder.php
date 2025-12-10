<?php
class DatabaseSeeder {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function run() {
        $this->createSuperAdmin();
        $this->createSampleCompany();
    }

    private function createSuperAdmin() {
        $password = password_hash('Admin123!', PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO users (first_name, last_name, email, password, role, is_active) 
                VALUES (?, ?, ?, ?, 'super_admin', 1)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['System', 'Administrator', 'admin@payslip.system', $password]);
        
        echo "Super admin created: admin@payslip.system / Admin123!\n";
    }

    private function createSampleCompany() {
        // Create sample company
        $sql = "INSERT INTO companies (name, slug, country, currency, timezone, plan) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['Acme Corporation', 'acme-corp', 'US', 'USD', 'America/New_York', 'premium']);
        $companyId = $this->pdo->lastInsertId();

        // Create company admin
        $password = password_hash('Admin123!', PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (tenant_id, first_name, last_name, email, password, role, is_active) 
                VALUES (?, ?, ?, ?, ?, 'company_admin', 1)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId, 'John', 'Smith', 'admin@acme.test', $password]);

        // Create sample department
        $sql = "INSERT INTO departments (tenant_id, name) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId, 'Engineering']);
        $deptId = $this->pdo->lastInsertId();

        // Create sample salary components
        $components = [
            ['BASIC', 'Basic Salary', 'earning', 'fixed', 0, null, 1],
            ['HOUSING', 'Housing Allowance', 'earning', 'percentage', 20, null, 1],
            ['TRANSPORT', 'Transport Allowance', 'earning', 'fixed', 200, null, 0],
            ['PENSION', 'Pension Contribution', 'deduction', 'percentage', 5, null, 0],
            ['TAX', 'Income Tax', 'deduction', 'formula', 0, 'BASIC * 0.15', 0]
        ];

        $sql = "INSERT INTO salary_components (tenant_id, component_code, name, type, calculation_type, default_value, formula, is_taxable) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        foreach ($components as $component) {
            $stmt->execute(array_merge([$companyId], $component));
        }

        // Create sample employee
        $sql = "INSERT INTO employees (tenant_id, employee_code, first_name, last_name, email, department_id, job_title, date_joined, employment_type) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $companyId, 'EMP-001', 'Jane', 'Doe', 'jane.doe@acme.test', $deptId, 
            'Senior Developer', '2024-01-15', 'full_time'
        ]);

        echo "Sample company 'Acme Corporation' created with admin: admin@acme.test / Admin123!\n";
    }
}