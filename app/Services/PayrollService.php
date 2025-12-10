<?php
namespace App\Services;

use App\Models\Employee;
use App\Models\SalaryComponent;
use App\Models\EmployeeSalary;
use App\Models\PayrollRun;
use App\Models\PayrollItem;
use App\Models\TaxRule;

class PayrollService {
    private $tenantId;

    public function __construct($tenantId) {
        $this->tenantId = $tenantId;
    }

    public function calculatePayroll($runMonth, $employeeIds = []) {
        $employees = $this->getEmployeesForPayroll($employeeIds);
        $payrollRunId = $this->createPayrollRun($runMonth);
        
        $results = [];
        $totalGross = 0;
        $totalNet = 0;

        foreach ($employees as $employee) {
            $calculation = $this->calculateEmployeePayroll($employee, $runMonth);
            $this->savePayrollItem($payrollRunId, $employee['id'], $calculation);
            
            $results[] = [
                'employee' => $employee,
                'calculation' => $calculation
            ];

            $totalGross += $calculation['gross'];
            $totalNet += $calculation['net_pay'];
        }

        $this->updatePayrollRunTotals($payrollRunId, $totalGross, $totalNet);

        return [
            'payroll_run_id' => $payrollRunId,
            'results' => $results,
            'totals' => [
                'gross' => $totalGross,
                'net' => $totalNet
            ]
        ];
    }

    private function calculateEmployeePayroll($employee, $runMonth) {
        $baseSalary = $this->getEmployeeBaseSalary($employee['id']);
        $components = $this->getSalaryComponents($employee['id']);
        
        $earnings = [];
        $deductions = [];
        $gross = $baseSalary;

        // Calculate earnings
        foreach ($components['earnings'] as $component) {
            $amount = $this->calculateComponentAmount($component, $baseSalary);
            $earnings[] = [
                'component' => $component['name'],
                'code' => $component['component_code'],
                'amount' => $amount,
                'is_taxable' => $component['is_taxable']
            ];
            $gross += $amount;
        }

        // Calculate pre-tax deductions
        $preTaxDeductions = 0;
        foreach ($components['deductions'] as $component) {
            if ($component['is_pre_tax'] ?? false) {
                $amount = $this->calculateComponentAmount($component, $baseSalary);
                $deductions[] = [
                    'component' => $component['name'],
                    'code' => $component['component_code'],
                    'amount' => $amount,
                    'type' => 'pre_tax'
                ];
                $preTaxDeductions += $amount;
            }
        }

        // Calculate taxable income
        $taxableIncome = $gross - $preTaxDeductions;

        // Calculate tax
        $taxAmount = $this->calculateTax($employee, $taxableIncome, $runMonth);
        $deductions[] = [
            'component' => 'Income Tax',
            'code' => 'TAX',
            'amount' => $taxAmount,
            'type' => 'tax'
        ];

        // Calculate post-tax deductions
        $postTaxDeductions = 0;
        foreach ($components['deductions'] as $component) {
            if (!($component['is_pre_tax'] ?? false)) {
                $amount = $this->calculateComponentAmount($component, $baseSalary);
                $deductions[] = [
                    'component' => $component['name'],
                    'code' => $component['component_code'],
                    'amount' => $amount,
                    'type' => 'post_tax'
                ];
                $postTaxDeductions += $amount;
            }
        }

        $totalDeductions = $preTaxDeductions + $taxAmount + $postTaxDeductions;
        $netPay = $gross - $totalDeductions;

        return [
            'base_salary' => $baseSalary,
            'gross' => $gross,
            'taxable_income' => $taxableIncome,
            'total_deductions' => $totalDeductions,
            'net_pay' => $netPay,
            'earnings' => $earnings,
            'deductions' => $deductions,
            'breakdown' => [
                'pre_tax_deductions' => $preTaxDeductions,
                'tax' => $taxAmount,
                'post_tax_deductions' => $postTaxDeductions
            ]
        ];
    }

    private function calculateComponentAmount($component, $baseSalary) {
        switch ($component['calculation_type']) {
            case 'fixed':
                return $component['default_value'];
            
            case 'percentage':
                return $baseSalary * ($component['default_value'] / 100);
            
            case 'formula':
                return $this->evaluateFormula($component['formula'], $baseSalary);
            
            default:
                return 0;
        }
    }

    private function evaluateFormula($formula, $baseSalary) {
        // Simple formula evaluator - in production, use a proper expression evaluator
        $formula = str_replace('BASIC', $baseSalary, $formula);
        
        // Remove any dangerous functions for security
        $formula = preg_replace('/[^0-9+\-*\/(). ]/', '', $formula);
        
        try {
            $result = eval("return $formula;");
            return is_numeric($result) ? $result : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calculateTax($employee, $taxableIncome, $runMonth) {
        $taxRules = (new TaxRule($this->tenantId))->getByCountry($employee['country'] ?? 'US');
        
        $taxAmount = 0;
        $remainingIncome = $taxableIncome;

        foreach ($taxRules as $rule) {
            if ($remainingIncome <= 0) break;

            $bracketAmount = min($remainingIncome, $rule['max_amount'] - $rule['min_amount']);
            $taxAmount += $bracketAmount * ($rule['rate'] / 100);
            $remainingIncome -= $bracketAmount;
        }

        return $taxAmount;
    }

    // Additional methods for payroll management...
}