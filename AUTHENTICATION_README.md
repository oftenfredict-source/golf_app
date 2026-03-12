# Golf Club Management System - Authentication & Login System

## Overview

This system implements a complete authentication flow with a splash screen progress bar animation (0-100%) that displays when login credentials are correct, then redirects to the dashboard.

## Features

✅ **Login Page with Splash Screen**
- Beautiful login interface with EmCa Technologies branding
- Password visibility toggle
- Form validation
- Progress bar animation (0-100%) on successful login
- Automatic redirect to dashboard after successful authentication

✅ **Dashboard Page**
- Full dashboard interface based on Materio template
- Golf club specific menu items
- User profile dropdown with logout functionality
- EmCa Technologies branding in footer

✅ **Authentication Flow**
- Secure login using Laravel's built-in authentication
- Session-based authentication
- Remember me functionality
- Proper error handling and validation

## File Structure

```
golfmis/
├── app/
│   └── Http/
│       └── Controllers/
│           ├── Auth/
│           │   └── AuthController.php      # Handles login/logout
│           └── DashboardController.php     # Dashboard controller
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php            # Login page with splash screen
│       └── dashboard/
│           └── index.blade.php            # Main dashboard page
└── routes/
    └── web.php                            # Routes configuration
```

## Routes

- `GET /login` - Display login form
- `POST /login` - Handle login authentication
- `POST /logout` - Handle user logout
- `GET /dashboard` - Display dashboard (protected)
- `GET /` - Redirects to dashboard if authenticated, otherwise to login

## How It Works

### Login Process

1. User enters credentials on login page
2. On form submission:
   - Form is submitted via AJAX (fetch API)
   - Splash screen appears immediately
   - Progress bar animates from 0% to 90%
   - Login request is sent to server
   - On successful login:
     - Progress completes to 100%
     - User is redirected to dashboard after 500ms
   - On failed login:
     - Splash screen hides
     - Error messages are displayed
     - User can retry login

### Splash Screen

The splash screen includes:
- Logo animation
- "Golf Club Management" title
- Loading message
- Progress bar (0-100%)
- Progress percentage display
- EmCa Technologies branding

### Dashboard Access

- Protected by `auth` middleware
- Requires valid session
- Shows user information in navigation
- Contains golf club specific menu items

## Customization

### Changing Splash Screen

Edit `resources/views/auth/login.blade.php`:
- Modify the splash screen styles (lines 50-130)
- Adjust progress animation speed (line 398, interval value)
- Change progress increment (line 397, progress += value)

### Changing Logo/Branding

- Update logo SVG in login page
- Update footer text in dashboard
- Modify app name in navigation

### Adding Menu Items

Edit `resources/views/dashboard/index.blade.php`:
- Add new menu items in the sidebar
- Update routes as needed

## Requirements

- Laravel 11.x
- PHP 8.2+
- MySQL/PostgreSQL database
- Assets folder in `public/assets/` directory

## Setup

1. Ensure you have a users table in your database
2. Create a user account (via tinker or seeder):
   ```php
   php artisan tinker
   User::create([
       'name' => 'Admin',
       'email' => 'admin@golfclub.com',
       'password' => bcrypt('password')
   ]);
   ```
3. Access the login page at `/login`
4. Use your credentials to login

## Assets

All assets should be located in:
- `public/assets/` directory

Required asset files:
- CSS: core.css, demo.css, page-auth.css
- JS: helpers.js, config.js, main.js, bootstrap.js, menu.js
- Images: favicon, avatars, illustrations
- Fonts: iconify-icons

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- IE11+ (with polyfills)
- Mobile responsive

## Powered By

**EmCa Technologies LTD**
- Website: https://emca.tech
- Email: emca@emca.tech
- Location: P.O. Box 20, Moshi – Kilimanjaro, Tanzania

---

For support or questions, contact EmCa Technologies.




