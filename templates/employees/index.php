<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees - Payslip System</title>
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
                <a href="/employees" class="flex items-center px-4 py-3 text-gray-700 bg-blue-50 border-r-2 border-blue-500">
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
                    <h1 class="text-2xl font-semibold text-gray-900">Employees</h1>
                    <div class="flex items-center space-x-4">
                        <a href="/employees/create" class="btn btn-primary">Add Employee</a>
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                        <a href="/auth/logout" class="text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <!-- Actions -->
                <div class="flex justify-between items-center mb-6">
                    <div class="flex space-x-4">
                        <button class="btn btn-secondary" onclick="openImportModal()">Import CSV</button>
                    </div>
                    <div class="w-64">
                        <input type="text" id="searchEmployees" placeholder="Search employees..." class="form-control">
                    </div>
                </div>

                <!-- Employees Table -->
                <div class="card">
                    <div class="overflow-x-auto">
                        <table class="table data-table">
                            <thead>
                                <tr>
                                    <th data-sort="employee_code">Employee Code</th>
                                    <th data-sort="first_name">Name</th>
                                    <th data-sort="email">Email</th>
                                    <th data-sort="department">Department</th>
                                    <th data-sort="job_title">Job Title</th>
                                    <th data-sort="date_joined">Date Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td class="font-mono"><?php echo htmlspecialchars($employee['employee_code']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($employee['email']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['department_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($employee['job_title'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($employee['date_joined'])); ?></td>
                                    <td>
                                        <div class="flex space-x-2">
                                            <a href="/employees/<?php echo $employee['id']; ?>" class="text-blue-600 hover:text-blue-900">View</a>
                                            <button class="text-green-600 hover:text-green-900" onclick="editEmployee(<?php echo $employee['id']; ?>)">Edit</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if (empty($employees)): ?>
                <div class="card p-8 text-center">
                    <div class="text-gray-500 mb-4">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No employees found</h3>
                    <p class="text-gray-500 mb-4">Get started by adding your first employee.</p>
                    <a href="/employees/create" class="btn btn-primary">Add Employee</a>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="modal hidden">
        <div class="modal-overlay fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full"></div>
        <div class="modal-container fixed top-20 left-1/2 transform -translate-x-1/2 w-96 bg-white rounded-lg shadow-lg">
            <div class="modal-content p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Import Employees from CSV</h3>
                <form id="importForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="file" name="file" accept=".csv" class="form-control" required>
                        <p class="text-sm text-gray-500 mt-1">Upload a CSV file with employee data</p>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeImportModal()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/app.js"></script>
    <script>
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        document.getElementById('importForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            try {
                const result = await api.uploadFile('/employees/import', formData.get('file'));
                api.showToast(`Imported ${result.success} employees successfully`);
                closeImportModal();
                setTimeout(() => window.location.reload(), 1000);
            } catch (error) {
                console.error('Import failed:', error);
            }
        });

        function editEmployee(employeeId) {
            window.location.href = `/employees/${employeeId}/edit`;
        }
    </script>
</body>
</html>