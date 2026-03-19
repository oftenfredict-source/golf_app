<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('settings.users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:admin,reception,counter,storekeeper,chef,waiter,manager',
            'phone' => 'required|string|max:20', // Phone is now required for SMS
        ]);

        // Auto-generate simple 6-digit password
        $password = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        ActivityLog::log('system', 'created', "User created: {$user->name} (Role: {$user->role})", 'User', $user->id);

        // Send SMS with credentials
        $smsSent = false;
        $smsError = null;
        try {
            $smsService = new SmsService();
            $message = "Welcome to Golf Club Management! Your account has been created.\n" .
                      "Email: {$user->email}\n" .
                      "Role: " . ucfirst($user->role) . "\n" .
                      "Password: {$password}\n" .
                      "Please login at: " . url('/login');
            
            $smsResult = $smsService->send($user->phone, $message);
            $smsSent = $smsResult['success'] ?? false;
            $smsError = $smsResult['message'] ?? null;
        } catch (\Exception $e) {
            Log::error("Failed to send staff registration SMS: " . $e->getMessage());
            $smsError = "SMS notification failed.";
        }

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.' . ($smsSent ? ' Password sent via SMS.' : ' But SMS failed: ' . $smsError),
            'user' => $user,
            'sms_sent' => $smsSent
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting last admin? Not strictly required but good practice.
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'role' => 'required|string|in:admin,reception,counter,storekeeper,chef,waiter,manager',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        ActivityLog::log('system', 'updated', "User updated: {$user->name}", 'User', $user->id);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 400);
        }

        $userName = $user->name;
        
        // Delete avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        ActivityLog::log('system', 'deleted', "User deleted: {$userName}", 'User', $id);

        return response()->json([
            'success' => true,
            'message' => 'User account successfully deleted.'
        ]);
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Generate a new 6-digit password
        $password = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Update user password
        $user->update([
            'password' => Hash::make($password),
        ]);

        ActivityLog::log('system', 'updated', "Admin reset password for user: {$user->name}", 'User', $user->id);

        // Send SMS with new password
        $smsSent = false;
        $smsError = null;
        if ($user->phone) {
            try {
                $smsService = new SmsService();
                $message = "Your Golf Club Management password has been reset by an administrator.\n" .
                           "New Password: {$password}\n" .
                           "Please login and change it for security.";
                
                $smsResult = $smsService->send($user->phone, $message);
                $smsSent = $smsResult['success'] ?? false;
                $smsError = $smsResult['message'] ?? null;
            } catch (\Exception $e) {
                Log::error("Failed to send password reset SMS: " . $e->getMessage());
                $smsError = "SMS notification failed.";
            }
        } else {
            $smsError = "User has no phone number on record.";
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.' . ($smsSent ? ' New password sent via SMS.' : ' But SMS failed: ' . $smsError),
            'password' => $password, // Also return it so admin can see/provide it manually
            'sms_sent' => $smsSent
        ]);
    }
}
