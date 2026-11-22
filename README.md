# Auto Repair Shop - Laravel Application

A full-featured Laravel 13 web application for managing an auto repair shop with role-based access control, repair job tracking, real-time notifications, and invoicing.

## 🎯 Features

### 🔐 Role-Based Access Control (RBAC)
- **Admin**: Full system control - manage users, services, jobs, and view reports
- **Manager**: Manage repair jobs, create invoices, assign mechanics
- **Mechanic**: View assigned jobs, update job status, add work notes
- **Customer**: Request repairs, track job history, view invoices, receive notifications

### 🔧 Core Features
- Complete CRUD operations for users (admin/manager only)
- Service catalog management
- Repair job tracking with status workflow (pending → assigned → in-progress → completed)
- Real-time notifications system
- Invoice generation and management
- Job assignment and progress tracking
- Customer-facing repair request portal
- Mechanic work assignment and updates
- Role-based dashboards with key metrics

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- SQLite (included)

### Quick Start

1. **Navigate to project directory**:
```bash
cd c:\Users\ADMIN\Desktop\auto-repair-shop\autorepairshop
```

2. **Start the development server** (environment already configured):
```bash
php artisan serve
```

3. **Access the application**:
   - Open browser to `http://localhost:8000`
   - Login page will load automatically

## 👥 Test Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@autorepairshop.test | password |
| Manager | manager@autorepairshop.test | password |
| Mechanic 1 | mechanic1@autorepairshop.test | password |
| Mechanic 2 | mechanic2@autorepairshop.test | password |
| Customer 1 | customer1@autorepairshop.test | password |
| Customer 2 | customer2@autorepairshop.test | password |
| Customer 3 | customer3@autorepairshop.test | password |

## 📊 Database Schema

### Core Tables
- `users` - User accounts with role assignment and contact info
- `roles` - System roles (admin, manager, mechanic, customer)
- `repair_jobs` - Main repair job records with status tracking
- `services` - Available repair services catalog
- `repair_job_services` - Many-to-many join table for job services
- `notifications` - User notifications system
- `invoices` - Invoice records for billing

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── AdminController.php          # Admin CRUD & Reports
│   ├── ManagerController.php        # Job & Invoice Management
│   ├── MechanicController.php       # Job Execution & Updates
│   ├── CustomerController.php       # Self-Service Portal
│   └── Auth/                        # Authentication Controllers
├── Models/
│   ├── User.php                     # User with role relationships
│   ├── Role.php                     # System roles
│   ├── RepairJob.php               # Core job entity
│   ├── Service.php                 # Service catalog
│   ├── Notification.php            # Notifications
│   └── Invoice.php                 # Invoicing
├── Policies/
│   ├── RepairJobPolicy.php         # Job authorization
│   ├── NotificationPolicy.php      # Notification access control
│   └── InvoicePolicy.php           # Invoice authorization
└── Middleware/
    └── CheckRole.php               # Role verification middleware

routes/
├── web.php                          # Main routes with role groups
└── auth.php                         # Authentication routes

resources/views/
├── admin/                          # Admin templates
├── manager/                        # Manager templates
├── mechanic/                       # Mechanic templates
├── customer/                       # Customer templates
└── auth/                           # Login/Register templates

database/
├── migrations/                     # Database schema (10 total)
└── seeders/                        # Sample data (17 users, 10 services)
```

## 🔄 User Workflows

### Customer Journey
1. Register/Login
2. Submit repair request
3. Receive assignment notifications
4. Track job progress in real-time
5. View & download invoices
6. Rate completed work

### Manager Workflow
1. View pending repair requests
2. Assign mechanics to jobs
3. Monitor job progress
4. Generate invoices
5. View revenue metrics

### Mechanic Operations
1. View assigned repair jobs
2. Update job status (in-progress, completed)
3. Add work notes & diagnostics
4. Mark job completion with costs

### Admin Controls
1. User management (CRUD for all roles)
2. Service catalog management
3. System-wide analytics dashboard
4. Generate business reports

## ⚡ Real-Time Features

The application is built with foundation for real-time functionality:
- **WebSocket broadcasting** via Pusher/Redis
- **Real-time notifications** when jobs are assigned or updated
- **Live status updates** as mechanics progress
- **Instant customer alerts** on job status changes

### To Enable Real-Time Features
```bash
php artisan install:broadcasting
```
Then configure Pusher credentials in `.env`:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
```

## 🔒 Security Features

- ✅ Role-based access control middleware
- ✅ Policy-based authorization for resources
- ✅ Protected routes with role verification
- ✅ CSRF protection on all forms
- ✅ Password hashing with bcrypt
- ✅ Email verification ready
- ✅ Rate limiting on auth endpoints

## 🛠️ Available Artisan Commands

```bash
# View database migration status
php artisan migrate:status

# Reset and reseed database
php artisan migrate:refresh --seed

# Access interactive shell
php artisan tinker

# Clear application caches
php artisan config:clear && php artisan cache:clear

# Generate application key
php artisan key:generate
```

## 🚧 Future Enhancements

- [ ] Real-time WebSocket notifications
- [ ] Mobile app REST API
- [ ] Payment gateway integration (Stripe)
- [ ] SMS notifications
- [ ] Email notifications with templates
- [ ] Advanced analytics & reporting
- [ ] Customer feedback & ratings
- [ ] Parts inventory management
- [ ] Appointment scheduling system
- [ ] Multi-location support

## 🐛 Troubleshooting

**Application Key Not Set**
```bash
php artisan key:generate
```

**Database Errors**
```bash
php artisan migrate:refresh --seed
```

**Cache Issues**
```bash
php artisan config:clear && php artisan cache:clear
```

## 📚 Documentation

- **Laravel Docs**: https://laravel.com/docs
- **Eloquent ORM**: https://laravel.com/docs/eloquent
- **Blade Templates**: https://laravel.com/docs/blade

## 📄 License

Open-source application built with Laravel.

---

**Status**: ✅ Ready for Development & Testing

The application is fully implemented with seeded test data. Start with `php artisan serve` and login with any test credential above.
