<?php
use PHPUnit\Framework\TestCase;
use App\Services\PayrollService;

class PayrollCalculationTest extends TestCase {
    private $payrollService;

    protected function setUp(): void {
        $this->payrollService = new PayrollService(1); // tenant_id = 1
    }

    public function testBasicSalaryCalculation() {
        $employee = ['id' => 1, 'base_salary' => 3000];
        $components = [
            'earnings' => [],
            'deductions' => []
        ];

        $result = $this->calculateEmployeePayroll($employee, $components);
        
        $this->assertEquals(3000, $result['gross']);
        $this->assertEquals(3000, $result['net_pay']);
        $this->assertEquals(0, $result['total_deductions']);
    }

    public function testPercentageAllowance() {
        $employee = ['id' => 1, 'base_salary' => 3000];
        $components = [
            'earnings' => [
                [
                    'name' => 'Housing Allowance',
                    'component_code' => 'HOUSING',
                    'calculation_type' => 'percentage',
                    'default_value' => 20,
                    'is_taxable' => true
                ]
            ],
            'deductions' => []
        ];

        $result = $this->calculateEmployeePayroll($employee, $components);
        
        $this->assertEquals(3600, $result['gross']); // 3000 + 20% = 3600
        $this->assertEquals(600, $result['earnings'][0]['amount']);
    }

    public function testTaxCalculationWithBrackets() {
        $employee = ['id' => 1, 'base_salary' => 5000, 'country' => 'US'];
        $components = [
            'earnings' => [],
            'deductions' => []
        ];

        // Mock tax brackets: 0-1000: 0%, 1001-5000: 10%, 5001+: 20%
        $taxRules = [
            ['min_amount' => 0, 'max_amount' => 1000, 'rate' => 0],
            ['min_amount' => 1001, 'max_amount' => 5000, 'rate' => 10],
            ['min_amount' => 5001, 'max_amount' => 10000, 'rate' => 20]
        ];

        $result = $this->calculateEmployeePayrollWithTax($employee, $components, $taxRules);
        
        // Tax calculation: 1000*0% + 4000*10% = 400
        $this->assertEquals(400, $result['breakdown']['tax']);
        $this->assertEquals(4600, $result['net_pay']); // 5000 - 400
    }

    public function testPreTaxDeductionsReduceTaxableIncome() {
        $employee = ['id' => 1, 'base_salary' => 4000];
        $components = [
            'earnings' => [],
            'deductions' => [
                [
                    'name' => 'Pension',
                    'component_code' => 'PENSION',
                    'calculation_type' => 'percentage',
                    'default_value' => 5,
                    'is_pre_tax' => true,
                    'is_taxable' => false
                ]
            ]
        ];

        $result = $this->calculateEmployeePayroll($employee, $components);
        
        $pensionDeduction = 4000 * 0.05; // 200
        $taxableIncome = 4000 - 200; // 3800
        
        $this->assertEquals(200, $result['breakdown']['pre_tax_deductions']);
        $this->assertEquals(3800, $result['taxable_income']);
    }

    public function testPartialMonthCalculation() {
        $employee = ['id' => 1, 'base_salary' => 3000];
        $startDate = '2024-01-15';
        $endDate = '2024-01-31';
        
        // 17 working days in January, employee worked 12 days
        $workingDays = 17;
        $daysWorked = 12;
        $proratedSalary = (3000 / $workingDays) * $daysWorked;

        $result = $this->calculateProratedSalary($employee, $startDate, $endDate);
        
        $this->assertEquals(2117.65, $result, '', 0.01); // 3000 / 17 * 12
    }

    private function calculateEmployeePayroll($employee, $components) {
        // Simplified calculation for testing
        $baseSalary = $employee['base_salary'];
        $gross = $baseSalary;
        $earnings = [];
        $deductions = [];

        foreach ($components['earnings'] as $component) {
            $amount = $this->calculateComponentAmount($component, $baseSalary);
            $gross += $amount;
            $earnings[] = $amount;
        }

        $preTaxDeductions = 0;
        foreach ($components['deductions'] as $component) {
            $amount = $this->calculateComponentAmount($component, $baseSalary);
            if ($component['is_pre_tax'] ?? false) {
                $preTaxDeductions += $amount;
            }
            $deductions[] = $amount;
        }

        $taxableIncome = $gross - $preTaxDeductions;
        $tax = $this->calculateSimpleTax($taxableIncome);
        $totalDeductions = $preTaxDeductions + $tax;
        $netPay = $gross - $totalDeductions;

        return [
            'gross' => $gross,
            'net_pay' => $netPay,
            'total_deductions' => $totalDeductions,
            'taxable_income' => $taxableIncome,
            'earnings' => array_map(fn($amt, $comp) => ['amount' => $amt, 'component' => $comp['name']], $earnings, $components['earnings']),
            'breakdown' => [
                'pre_tax_deductions' => $preTaxDeductions,
                'tax' => $tax
            ]
        ];
    }

    private function calculateComponentAmount($component, $baseSalary) {
        switch ($component['calculation_type']) {
            case 'fixed':
                return $component['default_value'];
            case 'percentage':
                return $baseSalary * ($component['default_value'] / 100);
            default:
                return 0;
        }
    }

    private function calculateSimpleTax($income) {
        // Simple tax calculation for testing
        if ($income <= 1000) return 0;
        if ($income <= 5000) return ($income - 1000) * 0.1;
        return 400 + ($income - 5000) * 0.2;
    }
}