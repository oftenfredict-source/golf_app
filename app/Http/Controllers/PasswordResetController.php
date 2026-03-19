<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Show the "Forgot Password" form (step 1: enter email/phone).
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP via SMS to the user's registered phone.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = trim($request->identifier);

        // Find user by email or phone
        $user = User::where('email', $identifier)
                    ->orWhere('phone', $identifier)
                    ->first();

        if (!$user) {
            return back()->withErrors(['identifier' => 'No account found with that email or phone number.'])->withInput();
        }

        if (!$user->phone) {
            return back()->withErrors(['identifier' => 'This account does not have a registered phone number. Please contact the administrator.'])->withInput();
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Invalidate any existing OTPs for this user
        DB::table('password_reset_otps')
            ->where('identifier', $user->email ?? $user->phone)
            ->update(['used' => true]);

        // Store new OTP (expires in 10 minutes)
        DB::table('password_reset_otps')->insert([
            'identifier' => $user->email ?? $user->phone,
            'otp'        => $otp,
            'used'       => false,
            'expires_at' => Carbon::now()->addMinutes(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Send OTP via SMS
        $smsService = new SmsService();
        $message = "Golf Club: Your password reset OTP is {$otp}. It expires in 10 minutes. Do not share this code.";
        $smsService->send($user->phone, $message);

        // Store session data for the reset step
        session([
            'reset_identifier' => $user->email ?? $user->phone,
            'reset_user_id'    => $user->id,
            'reset_phone_hint' => $this->maskPhone($user->phone),
        ]);

        return redirect()->route('password.reset.form')
            ->with('otp_sent', true)
            ->with('phone_hint', $this->maskPhone($user->phone));
    }

    /**
     * Show the OTP + new password form (step 2).
     */
    public function showResetForm()
    {
        if (!session('reset_identifier')) {
            return redirect()->route('password.forgot')
                ->withErrors(['identifier' => 'Session expired. Please request a new OTP.']);
        }

        return view('auth.reset-password', [
            'phone_hint' => session('reset_phone_hint'),
        ]);
    }

    /**
     * Verify OTP and reset the password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'otp'                  => 'required|digits:6',
            'password'             => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $identifier = session('reset_identifier');
        $userId     = session('reset_user_id');

        if (!$identifier || !$userId) {
            return redirect()->route('password.forgot')
                ->withErrors(['otp' => 'Session expired. Please request a new OTP.']);
        }

        // Validate OTP
        $record = DB::table('password_reset_otps')
            ->where('identifier', $identifier)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>=', Carbon::now())
            ->latest()
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        // Mark OTP as used
        DB::table('password_reset_otps')->where('id', $record->id)->update(['used' => true]);

        // Update user's password
        $user = User::findOrFail($userId);
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear session
        session()->forget(['reset_identifier', 'reset_user_id', 'reset_phone_hint']);

        return redirect()->route('login')
            ->with('success', 'Password reset successfully! You can now log in with your new password.');
    }

    /**
     * Mask phone number for display: e.g. 0712****567
     */
    private function maskPhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        $len = strlen($clean);
        if ($len <= 5) return $phone;
        return substr($clean, 0, 4) . str_repeat('*', $len - 7) . substr($clean, -3);
    }
}
