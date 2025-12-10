<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee - Payslip System</title>
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
        <div class="main-content flex-1 overflow-y-auto">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-900">Add Employee</h1>
                    <div class="flex items-center space-x-4">
                        <a href="/employees" class="btn btn-secondary">Back to List</a>
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                        <a href="/auth/logout" class="text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <div class="max-w-4xl mx-auto">
                    <div class="card p-6">
                        <form class="ajax-form space-y-6" action="/employees" method="POST" data-reload="true">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Personal Information -->
                                <div class="md:col-span-2">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                                </div>
                                
                                <div class="form-group">
                                    <label for="employee_code" class="form-label">Employee Code *</label>
                                    <input type="text" id="employee_code" name="employee_code" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" id="email" name="email" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" id="phone" name="phone" class="form-control">
                                </div>

                                <!-- Employment Information -->
                                <div class="md:col-span-2 mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Employment Information</h3>
                                </div>
                                
                                <div class="form-group">
                                    <label for="department_id" class="form-label">Department</label>
                                    <select id="department_id" name="department_id" class="form-control">
                                        <option value="">Select Department</option>
                                        <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="job_title" class="form-label">Job Title</label>
                                    <input type="text" id="job_title" name="job_title" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label for="date_joined" class="form-label">Date Joined *</label>
                                    <input type="date" id="date_joined" name="date_joined" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="employment_type" class="form-label">Employment Type</label>
                                    <select id="employment_type" name="employment_type" class="form-control">
                                        <option value="full_time">Full Time</option>
                                        <option value="part_time">Part Time</option>
                                        <option value="contract">Contract</option>
                                        <option value="temporary">Temporary</option>
                                    </select>
                                </div>

                                <!-- Bank Information -->
                                <div class="md:col-span-2 mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bank Information</h3>
                                </div>
                                
                                <div class="form-group">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <input type="text" id="bank_name" name="bank_name" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label for="bank_account" class="form-label">Bank Account Number</label>
                                    <input type="text" id="bank_account" name="bank_account" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label for="tax_id" class="form-label">Tax ID</label>
                                    <input type="text" id="tax_id" name="tax_id" class="form-control">
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                                <a href="/employees" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Employee</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/app.js"></script>
    <script>
        // Set today's date as default for date joined
        document.getElementById('date_joined').valueAsDate = new Date();
    </script>
</body>
</html>