<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslips - Payslip System</title>
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
                <a href="/payslips" class="flex items-center px-4 py-3 text-gray-700 bg-blue-50 border-r-2 border-blue-500">
                    <span>Payslips</span>
                </a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="main-content flex-1 overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-900">Payslips</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                        <a href="/auth/logout" class="text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <!-- Payslips Table -->
                <div class="card">
                    <div class="overflow-x-auto">
                        <table class="table data-table">
                            <thead>
                                <tr>
                                    <th data-sort="employee_name">Employee</th>
                                    <th data-sort="run_month">Payroll Month</th>
                                    <th data-sort="net_pay">Net Pay</th>
                                    <th data-sort="emailed">Emailed</th>
                                    <th data-sort="created_at">Generated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payslips as $payslip): ?>
                                <tr>
                                    <td class="font-medium">
                                        <?php echo htmlspecialchars($payslip['first_name'] . ' ' . $payslip['last_name']); ?>
                                        <span class="text-gray-500 text-sm block"><?php echo htmlspecialchars($payslip['employee_code']); ?></span>
                                    </td>
                                    <td><?php echo date('F Y', strtotime($payslip['run_month'])); ?></td>
                                    <td class="font-mono">$<?php echo number_format($payslip['net_pay'], 2); ?></td>
                                    <td>
                                        <?php if ($payslip['emailed']): ?>
                                            <span class="text-green-600">Yes</span>
                                        <?php else: ?>
                                            <span class="text-gray-500">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($payslip['created_at'])); ?></td>
                                    <td>
                                        <div class="flex space-x-2">
                                            <a href="/payslip/download/<?php echo $payslip['id']; ?>" class="text-blue-600 hover:text-blue-900" target="_blank">Download</a>
                                            <?php if (!$payslip['emailed']): ?>
                                            <button class="text-green-600 hover:text-green-900" onclick="emailPayslip(<?php echo $payslip['id']; ?>)">Email</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if (empty($payslips)): ?>
                <div class="card p-8 text-center">
                    <div class="text-gray-500 mb-4">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No payslips generated yet</h3>
                    <p class="text-gray-500 mb-4">Payslips will appear here after payroll runs are processed.</p>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/app.js"></script>
    <script>
        async function emailPayslip(payslipId) {
            try {
                await api.post(`/payslip/email/${payslipId}`);
                api.showToast('Payslip emailed successfully');
                setTimeout(() => window.location.reload(), 1000);
            } catch (error) {
                console.error('Email failed:', error);
            }
        }
    </script>
</body>
</html>