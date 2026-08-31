# SACCO Management System

A comprehensive web-based Savings and Credit Cooperative (SACCO) management system built with Laravel 12 and modern web technologies.

## 🏦 About SACCO System

This system provides complete financial management capabilities for SACCO operations including member management, savings accounts, loan processing, group savings, and financial reporting. It's designed to streamline cooperative operations and provide transparent financial services to members.

## ✨ Key Features

### Member Management
- Complete member registration and profile management
- Member financial accounts and summaries
- Soft delete functionality for member records
- Member search and filtering capabilities

### Loan Management
- Loan application and approval workflow
- Multiple loan types and interest calculation methods
- Repayment schedule generation and tracking
- Loan penalty management
- Loan status monitoring (pending, approved, disbursed, completed)

### Savings Management
- Individual member savings accounts
- Group savings with monthly contributions
- Deposit management and distribution
- Savings interest calculation
- Fine management for late contributions

### Financial Management
- Cash flow tracking and reporting
- Transaction management and auditing
- Fiscal year management
- Financial statements and reports
- Excel import/export capabilities

### User Management
- Role-based access control
- User authentication and authorization
- Profile management
- Activity logging

## 🛠 Technology Stack

### Backend
- **Framework**: Laravel 12
- **PHP Version**: ^8.2
- **Database**: SQLite (configurable for MySQL/PostgreSQL)
- **Authentication**: Laravel Breeze
- **Excel Processing**: Maatwebsite Excel

### Frontend
- **CSS Framework**: Tailwind CSS
- **JavaScript**: Alpine.js
- **Build Tool**: Vite
- **HTTP Client**: Axios

### Development Tools
- **Testing**: PHPUnit
- **Code Style**: Laravel Pint
- **Package Management**: Composer & npm
- **Local Development**: Laravel Sail

## 📋 System Requirements

- PHP 8.2 or higher
- Composer
- Node.js and npm
- SQLite (or MySQL/PostgreSQL)
- Web server (Apache/Nginx or PHP built-in server)

## 🚀 Installation

### Prerequisites
Ensure you have PHP 8.2+, Composer, and Node.js installed on your system.

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd saco_system
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   ```

5. **Build frontend assets**
   ```bash
   npm run build
   ```

6. **Start development server**
   ```bash
   php artisan serve
   ```

### Quick Setup Script
Use the provided composer script for automated setup:
```bash
composer run setup
```

## 🎯 Usage

### Development
Start the complete development environment with:
```bash
composer run dev
```
This will start:
- Laravel development server
- Queue worker
- Log viewer
- Vite frontend build

### Testing
Run the test suite:
```bash
composer run test
```

## Portable Windows Demo Package

To create a ZIP that runs without Docker, PHP, Node.js, Composer, Git, or a database server on the presentation PC:

```powershell
powershell -ExecutionPolicy Bypass -File .\package-demo.ps1
```

Run this on the development PC with Docker Desktop and Node.js available. The script builds the frontend, creates a fresh SQLite demo database with synthetic presentation data, bundles the Composer runtime dependencies and portable Windows PHP, and writes `dist\SACCO-Demo.zip`. The recipient extracts the ZIP and double-clicks `Start-SACCO.bat`; `Stop-SACCO.bat` shuts the demo down.

## 📁 Project Structure

```
saco_system/
├── app/
│   ├── Http/Controllers/          # Application controllers
│   │   ├── Admin/                # Admin-specific controllers
│   │   └── Auth/                 # Authentication controllers
│   ├── Models/                   # Eloquent models
│   ├── Services/                 # Business logic services
│   └── Observers/                # Model observers
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── resources/
│   ├── views/                    # Blade templates
│   └── js/                       # JavaScript files
├── routes/                       # Application routes
├── storage/                      # Application storage
└── public/                       # Public assets
```

## 🗄 Database Schema

### Core Tables
- **members**: Member information and profiles
- **users**: System users and authentication
- **loans**: Loan records and details
- **savings**: Individual savings accounts
- **monthly_savings**: Monthly contribution tracking
- **group_savings**: Group savings management
- **transactions**: Financial transactions
- **cash_flows**: Cash flow tracking
- **fiscal_years**: Fiscal year management

### Supporting Tables
- **loan_requests**: Loan application records
- **loan_repayments**: Loan repayment tracking
- **repayment_schedules**: Automated repayment schedules
- **loan_penalties**: Penalty management
- **member_accounts**: Member financial accounts
- **member_financials**: Financial summaries

## 🔧 Configuration

### Environment Variables
Key configuration options in `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# For MySQL/PostgreSQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sacco_db
# DB_USERNAME=root
# DB_PASSWORD=
```

### Database Configuration
The system defaults to SQLite for easy setup. To use MySQL or PostgreSQL:
1. Update `DB_CONNECTION` in `.env`
2. Configure database credentials
3. Run migrations: `php artisan migrate`

## 📊 Features in Detail

### Loan Management
- **Application Process**: Members can apply for loans with detailed information
- **Approval Workflow**: Admin approval system with status tracking
- **Interest Calculation**: Support for simple and compound interest
- **Repayment Scheduling**: Automated monthly repayment schedules
- **Penalty Management**: Late payment penalties and waivers

### Group Savings
- **Monthly Contributions**: Track member contributions to group savings
- **Deposit Management**: Handle deposits and distributions
- **Fine System**: Automated fines for late contributions
- **Reporting**: Monthly and annual savings reports

### Financial Reporting
- **Cash Flow Statements**: Detailed cash inflow/outflow tracking
- **Loan Portfolios**: Comprehensive loan status reports
- **Member Statements**: Individual member financial summaries
- **Excel Export**: Export reports to Excel format

## 🔐 Security Features

- User authentication and authorization
- Role-based access control
- CSRF protection
- SQL injection prevention
- Input validation and sanitization
- Secure password hashing

## 🧪 Testing

The system includes PHPUnit tests for core functionality:

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter LoanTest

# Generate test coverage report
php artisan test --coverage
```

## 📝 API Endpoints

The system provides RESTful API endpoints for:
- Member management (`/api/members`)
- Loan processing (`/api/loans`)
- Savings operations (`/api/savings`)
- Transaction records (`/api/transactions`)
- Financial reports (`/api/reports`)

## 🔄 Maintenance

### Database Maintenance
```bash
# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Reset database (development only)
php artisan migrate:fresh --seed
```

### Log Management
```bash
# View application logs
php artisan pail

# Clear old logs
php artisan log:clear
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Check the Laravel documentation at [laravel.com/docs](https://laravel.com/docs)
- Review the application logs for debugging information

## 📈 Roadmap

Planned future enhancements:
- Mobile application support
- Advanced reporting dashboards
- SMS notifications for members
- Integration with payment gateways
- Multi-branch support
- Advanced audit logging

---

**Built with ❤️ using Laravel and modern web technologies**
