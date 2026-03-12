<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SmsService;
use App\Models\DrivingRangeConfig;
use App\Models\RentalConfig;
use App\Models\AccessControlConfig;

class SettingsController extends Controller
{
    public function configuration()
    {
        $drivingRangeConfig = DrivingRangeConfig::getConfig();
        $rentalConfig = RentalConfig::getConfig();
        $accessControlConfig = AccessControlConfig::getConfig();
        
        return view('settings.configuration', compact('drivingRangeConfig', 'rentalConfig', 'accessControlConfig'));
    }
    
    public function saveAccessControlConfig(Request $request)
    {
        $request->validate([
            'members_only' => 'nullable|boolean',
            'require_valid_card' => 'nullable|boolean',
            'check_balance' => 'nullable|boolean',
            'allow_guests' => 'nullable|boolean',
            'operating_hours_only' => 'nullable|boolean',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'min_balance' => 'nullable|numeric|min:0',
            'guest_fee' => 'nullable|numeric|min:0',
            'blocked_cards' => 'nullable|string',
        ]);
        
        $config = AccessControlConfig::getConfig();
        
        $config->update([
            'members_only' => $request->has('members_only') ? (bool)$request->members_only : false,
            'require_valid_card' => $request->has('require_valid_card') ? (bool)$request->require_valid_card : false,
            'check_balance' => $request->has('check_balance') ? (bool)$request->check_balance : false,
            'allow_guests' => $request->has('allow_guests') ? (bool)$request->allow_guests : false,
            'operating_hours_only' => $request->has('operating_hours_only') ? (bool)$request->operating_hours_only : false,
            'opening_time' => $request->opening_time ?? '06:00',
            'closing_time' => $request->closing_time ?? '22:00',
            'min_balance' => $request->min_balance ?? 0,
            'guest_fee' => $request->guest_fee ?? 50000,
            'blocked_cards' => $request->blocked_cards ?? null,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Access control configuration saved successfully'
        ]);
    }
    public function communication()
    {
        $smsSettings = DB::table('sms_settings')->first();
        return view('settings.communication', compact('smsSettings'));
    }
    
    public function saveCommunicationSettings(Request $request)
    {
        $request->validate([
            'sms_username' => 'nullable|string|max:255',
            'sms_password' => 'nullable|string|max:255',
            'sms_from' => 'nullable|string|max:50',
            'sms_url' => 'nullable|url|max:500',
            'sms_enabled' => 'nullable|boolean',
        ]);
        
        $settings = DB::table('sms_settings')->first();
        
        if ($settings) {
            DB::table('sms_settings')->where('id', $settings->id)->update([
                'username' => $request->sms_username,
                'password' => $request->sms_password,
                'sender_name' => $request->sms_from ?? 'GolfClub',
                'api_url' => $request->sms_url,
                'enabled' => $request->sms_enabled ?? false,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('sms_settings')->insert([
                'username' => $request->sms_username,
                'password' => $request->sms_password,
                'sender_name' => $request->sms_from ?? 'GolfClub',
                'api_url' => $request->sms_url,
                'enabled' => $request->sms_enabled ?? false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Communication settings saved successfully'
        ]);
    }
    
    public function testSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^255[0-9]{9}$/',
        ]);
        
        $smsService = new SmsService();
        $result = $smsService->testSms($request->phone);
        
        return response()->json($result);
    }
}
