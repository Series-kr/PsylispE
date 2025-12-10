<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Settings - Payslip System</title>
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
                <a href="/reports" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50">
                    <span>Reports</span>
                </a>
                <a href="/settings" class="flex items-center px-4 py-3 text-gray-700 bg-blue-50 border-r-2 border-blue-500">
                    <span>Settings</span>
                </a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="main-content flex-1 overflow-y-auto">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-900">Company Settings</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                        <a href="/auth/logout" class="text-gray-600 hover:text-gray-900">Logout</a>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <div class="max-w-4xl mx-auto">
                    <div class="card p-6">
                        <form class="ajax-form space-y-6" action="/company/settings/update" method="POST">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group md:col-span-2">
                                    <label for="name" class="form-label">Company Name *</label>
                                    <input type="text" id="name" name="name" class="form-control" 
                                           value="<?php echo htmlspecialchars($company['name'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="form-group md:col-span-2">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea id="address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($company['address'] ?? ''); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="country" class="form-label">Country</label>
                                    <select id="country" name="country" class="form-control">
                                        <option value="">Select Country</option>
                                        <option value="US" <?php echo ($company['country'] ?? '') === 'US' ? 'selected' : ''; ?>>United States</option>
                                        <option value="UK" <?php echo ($company['country'] ?? '') === 'UK' ? 'selected' : ''; ?>>United Kingdom</option>
                                        <option value="CA" <?php echo ($company['country'] ?? '') === 'CA' ? 'selected' : ''; ?>>Canada</option>
                                        <option value="AU" <?php echo ($company['country'] ?? '') === 'AU' ? 'selected' : ''; ?>>Australia</option>
                                        <option value="IN" <?php echo ($company['country'] ?? '') === 'IN' ? 'selected' : ''; ?>>India</option>
                                        <option value="GH" <?php echo ($company['country'] ?? '') === 'GH' ? 'selected' : ''; ?>>Ghana</option>
                                        <option value="NG" <?php echo ($company['country'] ?? '') === 'NG' ? 'selected' : ''; ?>>Nigeria</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="currency" class="form-label">Currency</label>
                                    <select id="currency" name="currency" class="form-control">
                                        <option value="USD" <?php echo ($company['currency'] ?? 'USD') === 'USD' ? 'selected' : ''; ?>>USD - US Dollar</option>
                                        <option value="GBP" <?php echo ($company['currency'] ?? 'USD') === 'GBP' ? 'selected' : ''; ?>>GBP - British Pound</option>
                                        <option value="EUR" <?php echo ($company['currency'] ?? 'USD') === 'EUR' ? 'selected' : ''; ?>>EUR - Euro</option>
                                        <option value="CAD" <?php echo ($company['currency'] ?? 'USD') === 'CAD' ? 'selected' : ''; ?>>CAD - Canadian Dollar</option>
                                        <option value="AUD" <?php echo ($company['currency'] ?? 'USD') === 'AUD' ? 'selected' : ''; ?>>AUD - Australian Dollar</option>
                                        <option value="GHS" <?php echo ($company['currency'] ?? 'USD') === 'GHS' ? 'selected' : ''; ?>>GHS - Ghana Cedi</option>
                                        <option value="NGN" <?php echo ($company['currency'] ?? 'USD') === 'NGN' ? 'selected' : ''; ?>>NGN - Nigerian Naira</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select id="timezone" name="timezone" class="form-control">
                                        <option value="UTC" <?php echo ($company['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                        <option value="America/New_York" <?php echo ($company['timezone'] ?? 'UTC') === 'America/New_York' ? 'selected' : ''; ?>>Eastern Time (ET)</option>
                                        <option value="America/Chicago" <?php echo ($company['timezone'] ?? 'UTC') === 'America/Chicago' ? 'selected' : ''; ?>>Central Time (CT)</option>
                                        <option value="America/Los_Angeles" <?php echo ($company['timezone'] ?? 'UTC') === 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time (PT)</option>
                                        <option value="Europe/London" <?php echo ($company['timezone'] ?? 'UTC') === 'Europe/London' ? 'selected' : ''; ?>>London</option>
                                        <option value="Europe/Paris" <?php echo ($company['timezone'] ?? 'UTC') === 'Europe/Paris' ? 'selected' : ''; ?>>Paris</option>
                                        <option value="Asia/Kolkata" <?php echo ($company['timezone'] ?? 'UTC') === 'Asia/Kolkata' ? 'selected' : ''; ?>>India (IST)</option>
                                        <option value="Africa/Accra" <?php echo ($company['timezone'] ?? 'UTC') === 'Africa/Accra' ? 'selected' : ''; ?>>Ghana (GMT)</option>
                                        <option value="Africa/Lagos" <?php echo ($company['timezone'] ?? 'UTC') === 'Africa/Lagos' ? 'selected' : ''; ?>>Nigeria (WAT)</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Current Plan</label>
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <div class="font-medium text-gray-900"><?php echo ucfirst($company['plan'] ?? 'trial'); ?> Plan</div>
                                        <div class="text-sm text-gray-600">
                                            <?php if (($company['plan'] ?? 'trial') === 'trial'): ?>
                                                30-day free trial
                                            <?php elseif (($company['plan'] ?? 'trial') === 'basic'): ?>
                                                Basic features included
                                            <?php else: ?>
                                                All features unlocked
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </div>
                        </form>
                    </div>

                    <!-- Danger Zone -->
                    <div class="card p-6 mt-6 border-red-200 bg-red-50">
                        <h3 class="text-lg font-medium text-red-800 mb-4">Danger Zone</h3>
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium text-red-700">Delete Company Data</h4>
                                <p class="text-red-600 text-sm">Permanently delete all company data, employees, and payroll records.</p>
                            </div>
                            <button class="btn btn-danger" onclick="showDeleteModal()">Delete Company Data</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal hidden">
        <div class="modal-overlay fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full"></div>
        <div class="modal-container fixed top-20 left-1/2 transform -translate-x-1/2 w-96 bg-white rounded-lg shadow-lg">
            <div class="modal-content p-6">
                <h3 class="text-lg font-medium text-red-800 mb-4">Confirm Deletion</h3>
                <p class="text-red-600 mb-4">This action cannot be undone. All company data will be permanently deleted.</p>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
                    <button type="button" onclick="deleteCompanyData()" class="btn btn-danger">Delete Everything</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/api.js"></script>
    <script src="/assets/js/app.js"></script>
    <script>
        function showDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function deleteCompanyData() {
            if (confirm('Are you absolutely sure? This will delete ALL company data permanently!')) {
                // Implementation for company data deletion
                api.showToast('Company data deletion initiated', 'warning');
                closeDeleteModal();
            }
        }
    </script>
</body>
</html>