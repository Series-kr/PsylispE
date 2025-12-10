<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Payslip System</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="flex h-screen bg-gray-50">
        <!-- Simplified Sidebar for Employees -->
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
                <a href="/dashboard" class="flex items-center px-4 py-3 text-gray-700 bg-blue-50 border-r-2 border-blue-500">
                    <span>Dashboard</span>
                </a>
                <a href="/payslips" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50">
                    <span>My Payslips</span>
                </a>
                <a href="/auth/logout" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50">
                    <span>Logout</span>
                </a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="main-content flex-1 overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-900">Employee Dashboard</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                        <a href="/auth/logout" class="text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <!-- Employee Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="card p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Personal Information</h3>
                        <div class="space-y-2 text-sm">
                            <div><span class="font-medium">Employee Code:</span> EMP-001</div>
                            <div><span class="font-medium">Department:</span> Engineering</div>
                            <div><span class="font-medium">Job Title:</span> Senior Developer</div>
                            <div><span class="font-medium">Date Joined:</span> Jan 15, 2024</div>
                        </div>
                    </div>
                    
                    <div class="card p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Salary Information</h3>
                        <div class="space-y-2 text-sm">
                            <div><span class="font-medium">Base Salary:</span> $5,000.00</div>
                            <div><span class="font-medium">Last Payroll:</span> <?php echo date('F Y', strtotime('-1 month')); ?></div>
                            <div><span class="font-medium">Next Payroll:</span> <?php echo date('F Y'); ?></div>
                        </div>
                    </div>
                    
                    <div class="card p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="/payslips" class="block w-full btn btn-primary text-center">View My Payslips</a>
                            <button class="block w-full btn btn-secondary">Download Tax Documents</button>
                        </div>
                    </div>
                </div>

                <!-- Recent Payslips -->
                <div class="card">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Payslips</h3>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Payroll Month</th>
                                        <th>Net Pay</th>
                                        <th>Status</th>
                                        <th>Generated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo date('F Y', strtotime('-1 month')); ?></td>
                                        <td class="font-mono">$4,250.00</td>
                                        <td><span class="text-green-600">Paid</span></td>
                                        <td><?php echo date('M j, Y', strtotime('-5 days')); ?></td>
                                        <td>
                                            <a href="#" class="text-blue-600 hover:text-blue-900">Download</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php echo date('F Y', strtotime('-2 month')); ?></td>
                                        <td class="font-mono">$4,250.00</td>
                                        <td><span class="text-green-600">Paid</span></td>
                                        <td><?php echo date('M j, Y', strtotime('-35 days')); ?></td>
                                        <td>
                                            <a href="#" class="text-blue-600 hover:text-blue-900">Download</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>