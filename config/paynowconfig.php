<?php

return [
    'name' => 'Api',
    'paymenturl' => env('PAYMENT_PORTAL', 'https://ermedics.co.zw/paynow/'),
    'return_url' => env('PAYNOW_RETURN_URL', 'https://ermedics.co.zw/paynow/'),
    'integration_id' => env('PAYNOW_INTEGRATION_ID', ''),
    'integration_key' => env('PAYNOW_INTEGRATION_KEY', ''),
    'mode'             => env('PAYNOW_MODE', 'test'),
    'merchant_email'   => env('PAYNOW_MERCHANT_EMAIL', ''),
];
