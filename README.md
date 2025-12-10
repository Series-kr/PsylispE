# Multi-tenant Payslip & Employee Salary Management System

A comprehensive SaaS application for managing employee payroll, salary structures, and payslip generation with multi-tenant architecture.

## Features

- **Multi-tenant Architecture**: Isolated company data with tenant_id scoping
- **Role-based Access Control**: Super Admin, Company Admin, HR, Payroll, Employee
- **Employee Management**: CRUD operations with CSV bulk import
- **Salary Configuration**: Flexible salary components (earnings, deductions)
- **Payroll Processing**: Monthly/weekly payroll runs with approval workflow
- **Payslip Generation**: PDF generation, email distribution, employee self-service
- **Tax Management**: Configurable tax rules per country
- **Reports & Analytics**: Salary expenses, tax summaries, payroll history
- **Loan Management**: Employee loans with automatic payroll deductions
- **Attendance & Leave**: Integration with payroll for deductions/overtime
- **Audit Logging**: Comprehensive activity tracking
- **Security**: Encrypted sensitive data, RBAC, input validation

## Tech Stack

- **Backend**: PHP 7.4+/8.x, MySQL
- **Frontend**: HTML, CSS, Tailwind CSS, Bootstrap 5, JavaScript, AJAX
- **PDF Generation**: TCPDF/Dompdf
- **Deployment**: Docker, Nginx, PHP-FPM

## Quick Start

### Prerequisites
- Docker and Docker Compose
- PHP 7.4+ (for local development)
- MySQL 5.7+

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd payslip-system