<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        
        // Check if user is trying to login with email or username
        // For now, we'll use email field as Laravel default User model uses email
        // If you have a username field, uncomment the lines below and add username to fillable in User model
        // $field = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        // $credentials[$field] = $credentials['email'];
        // unset($credentials['email']);
        
        // Use email for authentication (default Laravel behavior)
        $email = $credentials['email'];
        $credentials = ['email' => $email, 'password' => $credentials['password']];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Check if this is an AJAX/JSON request
            $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
            
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('dashboard')
                ], 200);
            }
            
            return redirect()->intended(route('dashboard'));
        }

        // Check if this is an AJAX/JSON request for error handling
        $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
        
        if ($isAjax) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
                'errors' => [
                    'email' => ['The provided credentials do not match our records.']
                ]
            ], 422);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}

