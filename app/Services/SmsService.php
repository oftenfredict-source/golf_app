<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SmsService
{
    protected $settings;
    
    public function __construct()
    {
        $this->loadSettings();
    }
    
    protected function loadSettings()
    {
        $this->settings = DB::table('sms_settings')->first();
    }
    
    /**
     * Send SMS message
     * 
     * @param string $phone Phone number (format: 255XXXXXXXXX)
     * @param string $message Message content
     * @return array ['success' => bool, 'message' => string]
     */
    public function send($phone, $message)
    {
        if (!$this->settings || !$this->settings->enabled) {
            return [
                'success' => false,
                'message' => 'SMS service is not enabled or not configured'
            ];
        }
        
        if (empty($this->settings->username) || empty($this->settings->password) || empty($this->settings->api_url)) {
            return [
                'success' => false,
                'message' => 'SMS gateway is not properly configured'
            ];
        }
        
        try {
            // Format phone number - ensure it starts with 255
            // Remove all non-numeric characters
            $phoneNumber = preg_replace('/[^0-9]/', '', $phone);
            
            // If it doesn't start with 255, add it
            if (!str_starts_with($phoneNumber, '255')) {
                // Remove leading 0 if present
                $phoneNumber = ltrim($phoneNumber, '0');
                // Add 255 prefix
                $phoneNumber = '255' . $phoneNumber;
            }
            
            // Validate phone number format (must be 255 followed by 9 digits = 12 total)
            if (!preg_match('/^255[0-9]{9}$/', $phoneNumber)) {
                Log::error('SMS sending failed: Invalid phone number format', [
                    'original_phone' => $phone,
                    'formatted' => $phoneNumber,
                    'expected_format' => '255XXXXXXXXX (12 digits total)',
                    'length' => strlen($phoneNumber)
                ]);
                return [
                    'success' => false,
                    'message' => 'Invalid phone number format. Must be 255XXXXXXXXX (12 digits). Original: ' . $phone . ', Formatted: ' . $phoneNumber
                ];
            }
            
            // Prepare Basic Auth
            $auth = base64_encode($this->settings->username . ':' . $this->settings->password);
            
            // Prepare JSON body
            $body = json_encode([
                'from' => $this->settings->sender_name,
                'to' => $phoneNumber,
                'text' => $message,
                'reference' => 'golfclub_' . time()
            ]);
            
            Log::info('Attempting to send SMS', [
                'phone' => $phoneNumber,
                'sender' => $this->settings->sender_name,
                'url' => $this->settings->api_url,
                'message_preview' => substr($message, 0, 50) . (strlen($message) > 50 ? '...' : '')
            ]);
            
            // Check if URL contains '/api/sms/v1' - use POST with JSON, otherwise use GET with URL parameters
            $usePostMethod = strpos($this->settings->api_url, '/api/sms/v1') !== false || strpos($this->settings->api_url, '/api/') !== false;
            
            // Use cURL for more control
            $curl = curl_init();
            
            if ($usePostMethod) {
                // Use POST method with JSON body and Basic Auth
                curl_setopt_array($curl, [
                    CURLOPT_URL => $this->settings->api_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Basic ' . $auth,
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ],
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_USERAGENT => 'GolfClub-SMS-Client/1.0'
                ]);
            } else {
                // Use GET method with URL parameters (for /link/sms/v1 endpoints)
                $text = urlencode($message);
                $password = urlencode($this->settings->password);
                
                $url = $this->settings->api_url . 
                       '?username=' . urlencode($this->settings->username) . 
                       '&password=' . $password . 
                       '&from=' . urlencode($this->settings->sender_name) . 
                       '&to=' . $phoneNumber . 
                       '&text=' . $text;

                Log::debug('SMS API Request (GET)', [
                    'url' => $url,
                    'method' => 'GET',
                    'from' => $this->settings->sender_name,
                    'to' => $phoneNumber
                ]);

                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_USERAGENT => 'GolfClub-SMS-Client/1.0'
                ]);
            }
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            $curlErrno = curl_errno($curl);
            
            Log::info('SMS API Response', [
                'http_code' => $httpCode,
                'response' => $response,
                'phone' => $phoneNumber
            ]);
            
            if ($curlErrno) {
                $errorMsg = "cURL Error ({$curlErrno}): {$curlError}";
                Log::error('SMS cURL Error', [
                    'error_code' => $curlErrno,
                    'error_message' => $curlError,
                    'phone' => $phoneNumber
                ]);
                curl_close($curl);
                return [
                    'success' => false,
                    'message' => $errorMsg
                ];
            }
            
            curl_close($curl);
            
            // Check response
            if ($httpCode == 200) {
                $responseData = json_decode($response, true);
                $responseLower = strtolower($response ?? '');
                
                // Check if SMS was sent successfully
                if (strpos($responseLower, 'success') !== false || 
                    strpos($responseLower, '200') !== false ||
                    strpos($responseLower, 'accepted') !== false ||
                    strpos($responseLower, 'sent') !== false ||
                    ($responseData !== null && isset($responseData['success']) && $responseData['success']) ||
                    ($responseData !== null && !isset($responseData['error']))) {
                    
                    Log::info('SMS sent successfully', [
                        'phone' => $phoneNumber,
                        'sender' => $this->settings->sender_name,
                        'response' => $response
                    ]);
                    
                    return [
                        'success' => true,
                        'message' => 'SMS sent successfully',
                        'response' => $responseData ?? $response
                    ];
                } else {
                    $errorMsg = 'SMS API returned 200 but response indicates failure';
                    if ($responseData && isset($responseData['error'])) {
                        $errorMsg .= ': ' . $responseData['error'];
                    } elseif ($responseData && isset($responseData['message'])) {
                        $errorMsg .= ': ' . $responseData['message'];
                    }
                    
                    Log::warning('SMS API returned 200 but content indicates failure', [
                        'phone' => $phoneNumber,
                        'response' => $response,
                        'error' => $errorMsg
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => $errorMsg,
                        'response' => $responseData ?? $response
                    ];
                }
            } else {
                $errorMsg = "SMS failed with HTTP code {$httpCode}";
                if ($response) {
                    $errorMsg .= ': ' . substr($response, 0, 200);
                }
                
                Log::error('SMS failed with HTTP code', [
                    'http_code' => $httpCode,
                    'response' => $response,
                    'phone' => $phoneNumber,
                    'error' => $errorMsg
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorMsg,
                    'response' => $response
                ];
            }
        } catch (\Exception $e) {
            Log::error('SMS sending exception', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'phone' => $phone ?? 'unknown',
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error sending SMS: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Send SMS notification for top-up
     */
    public function sendTopupNotification($member, $amount, $balanceAfter)
    {
        if (!$member->phone) {
            return ['success' => false, 'message' => 'Member has no phone number'];
        }
        
        $message = "Dear {$member->name}, your account has been topped up with TZS " . number_format($amount) . ". New balance: TZS " . number_format($balanceAfter) . ". Thank you!";
        
        return $this->send($member->phone, $message);
    }
    
    /**
     * Send SMS notification for payment/deduction
     */
    public function sendPaymentNotification($member, $amount, $balanceAfter, $service = 'service')
    {
        if (!$member->phone) {
            return ['success' => false, 'message' => 'Member has no phone number'];
        }
        
        $message = "Dear {$member->name}, TZS " . number_format($amount) . " has been deducted from your account for {$service}. New balance: TZS " . number_format($balanceAfter) . ". Thank you!";
        
        return $this->send($member->phone, $message);
    }
    
    /**
     * Send SMS notification for refund
     */
    public function sendRefundNotification($member, $amount, $balanceAfter, $reason = 'refund')
    {
        if (!$member->phone) {
            return ['success' => false, 'message' => 'Member has no phone number'];
        }
        
        $message = "Dear {$member->name}, TZS " . number_format($amount) . " has been refunded to your account ({$reason}). New balance: TZS " . number_format($balanceAfter) . ". Thank you!";
        
        return $this->send($member->phone, $message);
    }
    
    /**
     * Send SMS notification for member registration
     */
    public function sendRegistrationNotification($member, $cardNumber, $upiId, $initialBalance = 0)
    {
        if (!$member->phone) {
            return ['success' => false, 'message' => 'Member has no phone number'];
        }
        
        $message = "Welcome {$member->name}! Your membership has been registered successfully. ";
        $message .= "Card Number: {$cardNumber}. ";
        $message .= "Member ID: {$member->member_id}. ";
        if ($initialBalance > 0) {
            $message .= "Initial Balance: TZS " . number_format($initialBalance) . ". ";
        }
        $message .= "Thank you for joining us!";
        
        return $this->send($member->phone, $message);
    }
    
    /**
     * Test SMS sending
     */
    public function testSms($phone, $message = null)
    {
        if (!$message) {
            $message = "Test message from Golf Club Management System. " . date('Y-m-d H:i:s');
        }
        
        return $this->send($phone, $message);
    }
}

