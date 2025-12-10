<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Payslip System</title>
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
                <a href="/payroll" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50">
                    <span>Payroll</span>
                </a>
                <a href="/payslips" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50">
                    <span>Payslips</span>
                </a>
                <a href="/reports" class="flex items-center px-4 py-3 text-gray-700 bg-blue-50 border-r-2 border-blue-500">
                    <span>Reports</span>
                </a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="main-content flex-1 overflow-y-auto">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-900">Reports & Analytics</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                        <a href="/auth/logout" class="text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <!-- Report Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="card p-6 cursor-pointer hover:shadow-md transition-shadow" onclick="showReport('payroll')">
                        <div class="flex items-center">
                            <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Payroll Summary</h3>
                                <p class="text-gray-500">Monthly and yearly payroll reports</p>
                            </div>
                        </div>
                    </div>

                    <div class="card p-6 cursor-pointer hover:shadow-md transition-shadow" onclick="showReport('tax')">
                        <div class="flex items-center">
                            <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Tax Reports</h3>
                                <p class="text-gray-500">Tax withholding and compliance reports</p>
                            </div>
                        </div>
                    </div>

                    <div class="card p-6 cursor-pointer hover:shadow-md transition-shadow" onclick="showReport('employee')">
                        <div class="flex items-center">
                            <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Employee Reports</h3>
                                <p class="text-gray-500">Employee statistics and department reports</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Content Area -->
                <div id="reportContent" class="card p-6">
                    <div class="text-center text-gray-500 py-12">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Select a Report</h3>
                        <p>Choose a report type from the options above to view detailed analytics.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/app.js"></script>
    <script>
        async function showReport(type) {
            const reportContent = document.getElementById('reportContent');
            
            switch(type) {
                case 'payroll':
                    await loadPayrollReport();
                    break;
                case 'tax':
                    await loadTaxReport();
                    break;
                case 'employee':
                    await loadEmployeeReport();
                    break;
            }
        }

        async function loadPayrollReport() {
            try {
                const result = await api.post('/reports/payroll-summary', {
                    year: new Date().getFullYear()
                });
                
                const data = result.data;
                document.getElementById('reportContent').innerHTML = `
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Payroll Summary Report</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">${data.total_payroll_runs}</div>
                            <div class="text-sm text-blue-800">Total Runs</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">$${data.total_gross.toFixed(2)}</div>
                            <div class="text-sm text-green-800">Total Gross</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">$${data.total_net.toFixed(2)}</div>
                            <div class="text-sm text-red-800">Total Net</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">$${data.total_tax.toFixed(2)}</div>
                            <div class="text-sm text-purple-800">Total Tax</div>
                        </div>
                    </div>
                `;
            } catch (error) {
                console.error('Failed to load payroll report:', error);
            }
        }

        async function loadTaxReport() {
            // Implementation for tax report
        }

        async function loadEmployeeReport() {
            // Implementation for employee report
        }
    </script>
</body>
</html>