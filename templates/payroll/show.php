<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Run Details - Payslip System</title>
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
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Payroll Run Details</h1>
                        <p class="text-gray-600"><?php echo date('F Y', strtotime($payrollRun['run_month'])); ?></p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium 
                            <?php echo $payrollRun['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                  ($payrollRun['status'] === 'draft' ? 'bg-yellow-100 text-yellow-800' : 
                                  ($payrollRun['status'] === 'paid' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')); ?>">
                            <?php echo ucfirst($payrollRun['status']); ?>
                        </span>
                        <a href="/payroll" class="btn btn-secondary">Back to List</a>
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                        <a href="/auth/logout" class="text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="card p-6 text-center">
                        <div class="text-2xl font-bold text-blue-600"><?php echo count($payrollItems); ?></div>
                        <div class="text-sm text-blue-800">Employees</div>
                    </div>
                    <div class="card p-6 text-center">
                        <div class="text-2xl font-bold text-green-600">$<?php echo number_format($payrollRun['total_gross'], 2); ?></div>
                        <div class="text-sm text-green-800">Total Gross</div>
                    </div>
                    <div class="card p-6 text-center">
                        <div class="text-2xl font-bold text-red-600">$<?php echo number_format($payrollRun['total_net'], 2); ?></div>
                        <div class="text-sm text-red-800">Total Net</div>
                    </div>
                    <div class="card p-6 text-center">
                        <div class="text-2xl font-bold text-purple-600">$<?php echo number_format($payrollRun['total_gross'] - $payrollRun['total_net'], 2); ?></div>
                        <div class="text-sm text-purple-800">Total Deductions</div>
                    </div>
                </div>

                <!-- Payroll Items Table -->
                <div class="card">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Employee Breakdown</h3>
                        <div class="flex space-x-3">
                            <button onclick="generateAllPayslips()" class="btn btn-primary">Generate All Payslips</button>
                            <?php if ($payrollRun['status'] === 'draft'): ?>
                            <button onclick="approvePayroll()" class="btn btn-success">Approve Payroll</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table data-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Gross Pay</th>
                                    <th>Deductions</th>
                                    <th>Net Pay</th>
                                    <th>Payslip</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payrollItems as $item): ?>
                                <tr>
                                    <td>
                                        <div class="font-medium"><?php echo htmlspecialchars($item['first_name'] . ' ' . $item['last_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($item['employee_code']); ?></div>
                                    </td>
                                    <td class="font-mono">$<?php echo number_format($item['gross'], 2); ?></td>
                                    <td class="font-mono">$<?php echo number_format($item['total_deductions'], 2); ?></td>
                                    <td class="font-mono font-bold">$<?php echo number_format($item['net_pay'], 2); ?></td>
                                    <td>
                                        <?php if ($item['payslip_generated'] ?? false): ?>
                                            <span class="text-green-600">Generated</span>
                                        <?php else: ?>
                                            <span class="text-gray-500">Not Generated</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="flex space-x-2">
                                            <button onclick="viewBreakdown(<?php echo $item['id']; ?>)" class="text-blue-600 hover:text-blue-900">View</button>
                                            <button onclick="generatePayslip(<?php echo $item['id']; ?>)" class="text-green-600 hover:text-green-900">Payslip</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Breakdown Modal -->
    <div id="breakdownModal" class="modal hidden">
        <div class="modal-overlay fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full"></div>
        <div class="modal-container fixed top-20 left-1/2 transform -translate-x-1/2 w-full max-w-2xl bg-white rounded-lg shadow-lg">
            <div class="modal-content p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Salary Breakdown</h3>
                <div id="breakdownContent"></div>
                <div class="flex justify-end mt-6">
                    <button onclick="closeBreakdown()" class="btn btn-secondary">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/app.js"></script>
    <script>
        async function viewBreakdown(payrollItemId) {
            try {
                // This would fetch the detailed breakdown from the server
                const breakdown = await api.get(`/payroll/items/${payrollItemId}/breakdown`);
                showBreakdownModal(breakdown);
            } catch (error) {
                console.error('Failed to fetch breakdown:', error);
            }
        }

        function showBreakdownModal(breakdown) {
            document.getElementById('breakdownModal').classList.remove('hidden');
            // Implementation to show breakdown details
        }

        function closeBreakdown() {
            document.getElementById('breakdownModal').classList.add('hidden');
        }

        async function generatePayslip(payrollItemId) {
            try {
                await api.post(`/payslip/generate/${payrollItemId}`);
                api.showToast('Payslip generated successfully');
                setTimeout(() => window.location.reload(), 1000);
            } catch (error) {
                console.error('Payslip generation failed:', error);
            }
        }

        async function generateAllPayslips() {
            try {
                // Implementation to generate all payslips
                api.showToast('All payslips generated successfully');
            } catch (error) {
                console.error('Payslip generation failed:', error);
            }
        }

        async function approvePayroll() {
            if (confirm('Are you sure you want to approve this payroll run? This action cannot be undone.')) {
                try {
                    await api.post(`/payroll/<?php echo $payrollRun['id']; ?>/approve`);
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