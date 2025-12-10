class PayslipApp {
    constructor() {
        this.api = api;
        this.init();
    }

    init() {
        this.initSidebar();
        this.initForms();
        this.initTables();
        this.initModals();
    }

    initSidebar() {
        const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
        const sidebar = document.querySelector('.sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
            });
        }

        // Mobile menu
        const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
            });
        }
    }

    initForms() {
        // Auto form submission with AJAX
        document.addEventListener('submit', async (e) => {
            const form = e.target;
            if (form.classList.contains('ajax-form')) {
                e.preventDefault();
                await this.handleFormSubmit(form);
            }
        });
    }

    async handleFormSubmit(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        try {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const response = await this.api.post(form.action, data);
            
            this.api.showToast(response.message || 'Operation completed successfully');
            
            if (response.redirect) {
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 1000);
            } else if (form.dataset.reload) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } catch (error) {
            console.error('Form submission error:', error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    initTables() {
        // Initialize data tables with sorting and filtering
        const tables = document.querySelectorAll('.data-table');
        
        tables.forEach(table => {
            this.enhanceTable(table);
        });
    }

    enhanceTable(table) {
        const headers = table.querySelectorAll('th[data-sort]');
        
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                this.sortTable(table, header.dataset.sort);
            });
        });

        // Add search functionality
        const searchInput = table.parentElement.querySelector('.table-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.filterTable(table, e.target.value);
            });
        }
    }

    sortTable(table, column) {
        // Implementation for table sorting
        console.log(`Sorting by ${column}`);
    }

    filterTable(table, query) {
        // Implementation for table filtering
        console.log(`Filtering with: ${query}`);
    }

    initModals() {
        // Modal handling
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-modal-toggle]')) {
                const modalId = e.target.dataset.modalTarget;
                this.toggleModal(modalId);
            }
            
            if (e.target.matches('[data-modal-close]') || e.target.matches('.modal-overlay')) {
                this.closeAllModals();
            }
        });
    }

    toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }

    closeAllModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.add('hidden');
        });
    }

    // Employee import functionality
    async importEmployees(file, onProgress) {
        try {
            const result = await this.api.uploadFile('/employees/import', file, onProgress);
            this.api.showToast(`Successfully imported ${result.success} employees`);
            
            if (result.errors.length > 0) {
                console.warn('Import errors:', result.errors);
                // Show errors in a modal or notification
            }
            
            return result;
        } catch (error) {
            this.api.showToast('Import failed: ' + error.message, 'error');
            throw error;
        }
    }

    // Payroll calculation
    async calculatePayroll(employeeIds = []) {
        try {
            const result = await this.api.post('/payroll/calculate', { employee_ids: employeeIds });
            this.api.showToast('Payroll calculated successfully');
            return result;
        } catch (error) {
            this.api.showToast('Payroll calculation failed: ' + error.message, 'error');
            throw error;
        }
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.app = new PayslipApp();
});