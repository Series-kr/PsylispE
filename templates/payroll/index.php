<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll - Payslip System</title>
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
        <div class="main-content flex-1 overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-900">Payroll Runs</h1>
                    <div class="flex items-center space-x-4">
                        <a href="/payroll/create" class="btn btn-primary">New Payroll Run</a>
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                        <a href="/auth/logout" class="text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <!-- Payroll Runs Table -->
                <div class="card">
                    <div class="overflow-x-auto">
                        <table class="table data-table">
                            <thead>
                                <tr>
                                    <th data-sort="run_month">Payroll Month</th>
                                    <th data-sort="status">Status</th>
                                    <th data-sort="total_gross">Total Gross</th>
                                    <th data-sort="total_net">Total Net</th>
                                    <th data-sort="run_by">Run By</th>
                                    <th data-sort="created_at">Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payrollRuns as $payroll): ?>
                                <tr>
                                    <td class="font-medium">
                                        <?php echo date('F Y', strtotime($payroll['run_month'])); ?>
                                    </td>
                                    <td>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            <?php echo $payroll['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                                  ($payroll['status'] === 'draft' ? 'bg-yellow-100 text-yellow-800' : 
                                                  ($payroll['status'] === 'paid' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $payroll['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="font-mono">$<?php echo number_format($payroll['total_gross'], 2); ?></td>
                                    <td class="font-mono">$<?php echo number_format($payroll['total_net'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($payroll['run_by_name']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($payroll['created_at'])); ?></td>
                                    <td>
                                        <div class="flex space-x-2">
                                            <a href="/payroll/<?php echo $payroll['id']; ?>" class="text-blue-600 hover:text-blue-900">View</a>
                                            <?php if ($payroll['status'] === 'draft'): ?>
                                            <button class="text-green-600 hover:text-green-900" onclick="approvePayroll(<?php echo $payroll['id']; ?>)">Approve</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if (empty($payrollRuns)): ?>
                <div class="card p-8 text-center">
                    <div class="text-gray-500 mb-4">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5 5.5l4.5-4.5M9 14l6-6m-5.5 5.5l4.5-4.5" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No payroll runs yet</h3>
                    <p class="text-gray-500 mb-4">Get started by creating your first payroll run.</p>
                    <a href="/payroll/create" class="btn btn-primary">Create Payroll Run</a>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/app.js"></script>
    <script>
        async function approvePayroll(payrollId) {
            if (confirm('Are you sure you want to approve this payroll run?')) {
                try {
                    await api.post(`/payroll/${payrollId}/approve`);
                    api.showToast('Payroll approved successfully');
                    setTimeout(() => window.location.reload(), 1000);
                } catch (error) {
                    console.error('Approval failed:', error);
                }
            }
        }
    </script>
</body>
</html>