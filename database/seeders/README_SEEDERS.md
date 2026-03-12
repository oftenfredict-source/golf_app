# Database Seeders Documentation

## Overview

This document describes the database seeders available for the Golf Club Management System.

## Available Seeders

### UserSeeder

Creates initial user accounts for the golf club management system with various roles and permissions.

**Run Command:**
```bash
php artisan db:seed --class=UserSeeder
```

**Or run all seeders:**
```bash
php artisan db:seed
```

## User Accounts Created

| Name | Email | Password | Role |
|------|-------|----------|------|
| System Administrator | admin@golfclub.com | admin123 | Full system access |
| General Manager | manager@golfclub.com | manager123 | Management access |
| Operations Manager | operations@golfclub.com | operations123 | Operations management |
| Finance Manager | finance@golfclub.com | finance123 | Financial management |
| Premium Counter Staff | counter.premium@golfclub.com | counter123 | Premium counter operations |
| Regular Counter Staff | counter.regular@golfclub.com | counter123 | Regular counter operations |
| Golf Operations Staff | golf.ops@golfclub.com | golf123 | Golf operations |
| Equipment Rental Staff | equipment@golfclub.com | equipment123 | Equipment management |
| Access Control Staff | access.control@golfclub.com | access123 | Entry gate management |
| Waiter/Service Staff | waiter@golfclub.com | waiter123 | Hospitality services |
| Test User | test@golfclub.com | test123 | Development/testing |

## User Roles & Permissions (Conceptual)

While the current implementation uses a simple user model, these seeders prepare for role-based access control:

### System Administrator
- Full system access
- User management
- System configuration
- All reports and analytics

### Managers (General, Operations, Finance)
- View all operations in their domain
- Generate reports
- Approve transactions
- Manage staff assignments

### Counter Staff
- Process orders
- Handle payments
- Manage customer accounts
- Generate receipts

### Golf Operations Staff
- Manage golf ball distribution
- Track equipment rentals
- Monitor driving range usage
- Record equipment returns

### Equipment Rental Staff
- Issue equipment
- Track equipment inventory
- Process rental payments
- Manage equipment maintenance

### Access Control Staff
- Register customers
- Monitor entry gates
- Validate UPI cards
- Manage visitor access

### Service Staff (Waiters)
- Take orders
- Process food/beverage orders
- Update order status
- Handle customer service

## Security Notes

⚠️ **IMPORTANT:** These are default passwords for initial setup. 

**Before going to production:**
1. Change all default passwords
2. Implement role-based access control
3. Add password complexity requirements
4. Enable two-factor authentication for admin accounts
5. Review and update user permissions
6. Set up proper user roles and permissions system

## Resetting Database

To reset the database and re-seed:

```bash
# Fresh migration and seeding
php artisan migrate:fresh --seed

# Or reset without dropping database
php artisan migrate:refresh --seed
```

## Adding New Seeders

To create a new seeder:

```bash
php artisan make:seeder YourSeederName
```

Then add it to `DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        UserSeeder::class,
        YourSeederName::class,
    ]);
}
```

## Example: Creating Custom Users

If you need to create additional users manually, you can use Laravel Tinker:

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'New User',
    'email' => 'newuser@golfclub.com',
    'password' => Hash::make('securepassword'),
    'email_verified_at' => now(),
]);
```

## Future Seeders to Consider

- CustomerSeeder (for registered members and visitors)
- EquipmentSeeder (golf clubs, balls, etc.)
- MenuItemsSeeder (food and beverage items)
- PaymentMethodsSeeder (UPI configurations)
- SettingsSeeder (system settings)
- InventorySeeder (initial stock levels)

---

**Powered by EmCa Technologies**




