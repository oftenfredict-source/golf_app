<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Show UPI management page
     */
    public function upiManagement()
    {
        return view('payments.upi-management');
    }

    /**
     * Show transactions page
     */
    public function transactions()
    {
        return view('payments.transactions');
    }

    /**
     * Show top-ups page
     */
    public function topUps()
    {
        return view('payments.top-ups');
    }

    /**
     * Generate QR code for UPI card
     */
    public function generateQRCode(Request $request)
    {
        $request->validate([
            'upi_id' => 'required|string',
            'customer_name' => 'required|string',
            'card_number' => 'required|string',
        ]);

        // Generate QR code data (UPI payment URL format)
        $upiUrl = "upi://pay?pa={$request->upi_id}&pn={$request->customer_name}&mc=5411&tn=Golf%20Club%20Payment&am=&cu=TZS";
        
        return response()->json([
            'success' => true,
            'qr_data' => $upiUrl,
            'upi_id' => $request->upi_id,
            'customer_name' => $request->customer_name,
            'card_number' => $request->card_number,
        ]);
    }
}




