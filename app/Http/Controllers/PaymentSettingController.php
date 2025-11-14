<?php

namespace App\Http\Controllers;

use App\Models\PaymentSetting;

class PaymentSettingController extends Controller
{
    public function showPayment()
    {
        $payment_setting = PaymentSetting::first(); // or with ID
        return view('your-view-name', compact('payment_setting'));
    }
}