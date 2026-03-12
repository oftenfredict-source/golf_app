# Login Troubleshooting Guide

## Common Issues and Solutions

### Issue: "An error occurred. Please try again."

This generic error can occur for several reasons. Follow these steps to debug:

#### 1. Check Browser Console
Open your browser's Developer Tools (F12) and check the Console tab for detailed error messages.

#### 2. Check Network Tab
- Open Developer Tools → Network tab
- Try logging in
- Look for the `/login` POST request
- Check the response status and content

#### 3. Verify Database Connection
Ensure your database is properly configured in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=golf_club
DB_USERNAME=root
DB_PASSWORD=
```

Test connection:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

#### 4. Verify Users Exist
Check if users are seeded:

```bash
php artisan tinker
>>> App\Models\User::count();
>>> App\Models\User::all(['name', 'email']);
```

If no users exist, run seeders:
```bash
php artisan db:seed --class=UserSeeder
```

#### 5. Test Credentials
Try logging in with:
- **Email:** admin@golfclub.com
- **Password:** admin123

#### 6. Check Laravel Logs
Check for errors in `storage/logs/laravel.log`:

```bash
# View last 50 lines
tail -n 50 storage/logs/laravel.log

# Or on Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 50
```

#### 7. Verify Routes
Check if routes are registered:

```bash
php artisan route:list | grep login
```

Should show:
- GET|HEAD  login ... AuthController@showLoginForm
- POST      login ... AuthController@login

#### 8. Clear Cache
Clear all Laravel caches:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### 9. Check CSRF Token
The CSRF token should be automatically included. Verify:
- The form has `@csrf` directive
- JavaScript is enabled in browser
- No browser extensions blocking requests

#### 10. Test Without JavaScript
Temporarily disable JavaScript and try submitting the form normally to see if the issue is JavaScript-related.

## Specific Error Messages

### "Network error. Please check your connection and try again."
- Check internet connection
- Verify server is running: `php artisan serve`
- Check for CORS issues
- Verify `.env` APP_URL is correct

### "Invalid credentials. Please check your email and password."
- Verify user exists in database
- Check password is correct (default: admin123)
- Ensure password is properly hashed in database
- Try resetting password:
  ```bash
  php artisan tinker
  >>> $user = App\Models\User::where('email', 'admin@golfclub.com')->first();
  >>> $user->password = Hash::make('admin123');
  >>> $user->save();
  ```

### "Failed to fetch"
- Check if server is running
- Verify URL is correct
- Check browser console for CORS errors
- Try accessing the login URL directly in browser

### CSRF Token Mismatch
- Clear browser cookies
- Check session driver in `.env`: `SESSION_DRIVER=file`
- Ensure storage/framework/sessions directory is writable
- Try in incognito/private browsing mode

## Debug Mode

Enable debug mode in `.env` to see detailed errors:

```env
APP_DEBUG=true
APP_ENV=local
```

⚠️ **Remember to set `APP_DEBUG=false` in production!**

## Quick Test Commands

```bash
# Check if migrations ran
php artisan migrate:status

# Run migrations if needed
php artisan migrate

# Seed users
php artisan db:seed --class=UserSeeder

# Test database connection
php artisan tinker
>>> App\Models\User::first();

# Check routes
php artisan route:list

# Clear all caches
php artisan optimize:clear
```

## Manual Login Test

Test login directly via Tinker:

```bash
php artisan tinker
>>> $user = App\Models\User::where('email', 'admin@golfclub.com')->first();
>>> Auth::login($user);
>>> Auth::check(); // Should return true
>>> Auth::user(); // Should show user details
```

## Contact Support

If issues persist:
1. Check `storage/logs/laravel.log` for detailed errors
2. Check browser console for JavaScript errors
3. Check Network tab for failed requests
4. Verify all requirements are met:
   - PHP >= 8.2
   - Composer dependencies installed
   - Database configured and accessible
   - Storage directories writable

---

**Powered by EmCa Technologies**




