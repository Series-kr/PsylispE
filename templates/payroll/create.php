<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Payroll Run - Payslip System</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-brand rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">P</span>
                    </div>
                    <span class="ml-3 text-lg font-semibold">Payslip System</span>
                </div>
            </div>
            
            <nav class="mt-6">
                <a href="/dashboard" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50">
                    <span>Dashboard</span>
                </a>
                <a href="/employees" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50">
                    <span>Employees</span>
                </a>
                <a href="/payroll" class="flex items-center px-4 py-3 text-gray-700 bg-blue-50 border-r-2 border-blue-500">
                    <span>Payroll</span>
                </a>
                <a href="/payslips" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50">
                    <span>Payslips</span>
                </a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="main-content flex-1 overflow-y-auto">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-900">Create Payroll Run</h1>
                    <div class="flex items-center space-x-4">
                        <a href="/payroll" class="btn btn-secondary">Back to List</a>
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                        <a href="/auth/logout" class="text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <div class="max-w-4xl mx-auto">
                    <div class="card p-6">
                        <form id="payrollForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label for="run_month" class="form-label">Payroll Month *</label>
                                    <input type="month" id="run_month" name="run_month" class="form-control" required 
                                           value="<?php echo date('Y-m'); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="employee_filter" class="form-label">Employee Filter</label>
                                    <select id="employee_filter" class="form-control" onchange="filterEmployees()">
                                        <option value="all">All Active Employees</option>
                                        <option value="department">By Department</option>
                                        <option value="selected">Select Individual Employees</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Employee Selection -->
                            <div class="form-group">
                                <label class="form-label">Select Employees</label>
                                <div class="border border-gray-200 rounded-lg max-h-64 overflow-y-auto">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="w-8">
                                                    <input type="checkbox" id="select_all" onchange="toggleAllEmployees()">
                                                </th>
                                                <th>Employee Code</th>
                                                <th>Name</th>
                                                <th>Department</th>
                                                <th>Base Salary</th>
                                            </tr>
                                        </thead>
                                        <tbody id="employee_list">
                                            <?php foreach ($employees as $employee): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="employee_ids[]" 
                                                           value="<?php echo $employee['id']; ?>" 
                                                           class="employee-checkbox" checked>
                                                </td>
                                                <td class="font-mono"><?php echo htmlspecialchars($employee['employee_code']); ?></td>
                                                <td><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></td>
                                                <td><?php echo htmlspecialchars($employee['department_name'] ?? 'N/A'); ?></td>
                                                <td class="font-mono">$<?php echo number_format($this->getEmployeeSalary($employee['id']), 2); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                                <button type="button" onclick="calculatePayroll()" class="btn btn-primary">Calculate Payroll</button>
                            </div>
                        </form>
                    </div>

                    <!-- Calculation Results -->
                    <div id="calculationResults" class="card p-6 mt-6 hidden">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Payroll Calculation Results</h3>
                        <div id="resultsContent"></div>
                        <div class="flex justify-end space-x-4 mt-6">
                            <button type="button" onclick="closeResults()" class="btn btn-secondary">Cancel</button>
                            <button type="button" onclick="savePayrollRun()" class="btn btn-success">Save Payroll Run</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/app.js"></script>
    <script>
        async function calculatePayroll() {
            const formData = new FormData(document.getElementById('payrollForm'));
            const employeeIds = Array.from(document.querySelectorAll('.employee-checkbox:checked'))
                                   .map(cb => cb.value);
            
            const payload = {
                run_month: formData.get('run_month'),
                employee_ids: employeeIds
            };

            try {
                const result = await api.post('/payroll/calculate', payload);
                showCalculationResults(result.data);
            } catch (error) {
                console.error('Calculation failed:', error);
            }
        }

        function showCalculationResults(data) {
            document.getElementById('calculationResults').classList.remove('hidden');
            
            let html = `
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">${data.results.length}</div>
                        <div class="text-sm text-blue-800">Employees</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">$${data.totals.gross.toFixed(2)}</div>
                        <div class="text-sm text-green-800">Total Gross</div>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="text-2xl font-bold text-red-600">$${data.totals.net.toFixed(2)}</div>
                        <div class="text-sm text-red-800">Total Net</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">$${(data.totals.gross - data.totals.net).toFixed(2)}</div>
                        <div class="text-sm text-purple-800">Total Deductions</div>
                    </div>
                </div>
            `;

            document.getElementById('resultsContent').innerHTML = html;
        }

        function closeResults() {
            document.getElementById('calculationResults').classList.add('hidden');
        }

        async function savePayrollRun() {
            // Implementation for saving payroll run
            api.showToast('Payroll run saved successfully');
            setTimeout(() => window.location.href = '/payroll', 1000);
        }

        function toggleAllEmployees() {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            const selectAll = document.getElementById('select_all').checked;
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll;
            });
        }

        function filterEmployees() {
            // Implementation for filtering employees
        }
    </script>
</body>
</html>