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
                <a href="/dashboard" class="flex items-center px-4 py-3 text-gray-700 bg-blue-50 border-r-2 border-blue-500">
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
            </nav>
        </div>

        <!-- Main content -->
        <div class="main-content flex-1 overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                        <a href="/auth/logout" class="text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="card p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Total Employees</h3>
                        <p class="text-3xl font-bold text-brand"><?php echo $totalEmployees; ?></p>
                    </div>
                    
                    <div class="card p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Active Payroll Runs</h3>
                        <p class="text-3xl font-bold text-accent"><?php echo count($activePayrolls); ?></p>
                    </div>
                    
                    <div class="card p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">This Month</h3>
                        <p class="text-3xl font-bold text-success"><?php echo date('F Y'); ?></p>
                    </div>
                </div>

                <!-- Recent Payroll Runs -->
                <div class="card">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Payroll Runs</h3>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($recentPayrolls)): ?>
                            <div class="overflow-x-auto">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Status</th>
                                            <th>Run By</th>
                                            <th>Total Gross</th>
                                            <th>Total Net</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentPayrolls as $payroll): ?>
                                        <tr>
                                            <td><?php echo date('F Y', strtotime($payroll['run_month'])); ?></td>
                                            <td>
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    <?php echo $payroll['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                                          ($payroll['status'] === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'); ?>">
                                                    <?php echo ucfirst($payroll['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($payroll['run_by_name']); ?></td>
                                            <td>$<?php echo number_format($payroll['total_gross'], 2); ?></td>
                                            <td>$<?php echo number_format($payroll['total_net'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 text-center py-4">No payroll runs yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>