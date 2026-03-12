# Quick Start Guide - Database Seeders

## Running Seeders

### Run All Seeders
```bash
php artisan db:seed
```

### Run Specific Seeder
```bash
php artisan db:seed --class=UserSeeder
```

### Fresh Migration + Seed (⚠️ Drops all data)
```bash
php artisan migrate:fresh --seed
```

### Reset and Re-seed (⚠️ Drops all data)
```bash
php artisan migrate:refresh --seed
```

## Default Login Credentials

After running the seeders, you can login with any of these accounts:

### Administrator Access
- **Email:** admin@golfclub.com
- **Password:** admin123

### Manager Access
- **Email:** manager@golfclub.com
- **Password:** manager123

### Test Account
- **Email:** test@golfclub.com
- **Password:** test123

### All Other Accounts
See `database/seeders/README_SEEDERS.md` for complete list of user accounts.

## Important Security Notes

🔒 **Before Production:**
1. Change ALL default passwords
2. Remove or disable test accounts
3. Implement proper role-based access control
4. Enable two-factor authentication
5. Review user permissions

## Troubleshooting

### Seeder Fails with Duplicate Entry Error
If you see "Duplicate entry" errors, the users may already exist. Options:

1. **Delete existing users and re-seed:**
   ```bash
   php artisan tinker
   App\Models\User::truncate();
   exit
   php artisan db:seed --class=UserSeeder
   ```

2. **Use fresh migration:**
   ```bash
   php artisan migrate:fresh --seed
   ```

### Seeder Runs but No Users Created
- Check database connection in `.env`
- Verify migrations have run: `php artisan migrate:status`
- Check for errors in Laravel logs: `storage/logs/laravel.log`

## Quick Reference: Common Seeder Commands

```bash
# Check migration status
php artisan migrate:status

# View all seeded users (via tinker)
php artisan tinker
>>> App\Models\User::all(['name', 'email']);
>>> exit

# Reset password for a user
php artisan tinker
>>> $user = App\Models\User::where('email', 'admin@golfclub.com')->first();
>>> $user->password = Hash::make('newpassword');
>>> $user->save();
>>> exit
```

---

**Need Help?** Contact EmCa Technologies support.




