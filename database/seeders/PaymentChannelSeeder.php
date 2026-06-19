<?php

namespace Database\Seeders;

use App\Models\PaymentChannel;
use App\Models\PaymentChannelParameter;
use Illuminate\Database\Seeder;

class PaymentChannelSeeder extends Seeder
{
    public function run(): void
    {
        // ── Manual / Bank Transfer ──────────────────────────────────────
        PaymentChannel::updateOrCreate(
            ['slug' => 'manual'],
            [
                'name'        => 'Manual / Bank Transfer',
                'is_active'   => true,
                'description' => 'Customer pays by bank transfer and admin verifies the payment.',
            ]
        );

        // ── Paynow ──────────────────────────────────────────────────────
        $paynow = PaymentChannel::updateOrCreate(
            ['slug' => 'paynow'],
            [
                'name'        => 'Paynow',
                'is_active'   => true,
                'description' => 'Online payment via Paynow (Zimbabwe). Customers are redirected to Paynow to pay.',
            ]
        );

        // Seed integration credentials from .env so admins don't need to enter them manually.
        // The keys match exactly what PaynowRepository::resolveCredentials() looks for.
        $params = [
            'IntegrationId'  => env('PAYNOW_INTEGRATION_ID',  ''),
            'Integrationkey' => env('PAYNOW_INTEGRATION_KEY', ''),
        ];

        foreach ($params as $key => $value) {
            PaymentChannelParameter::updateOrCreate(
                ['payment_channel_id' => $paynow->id, 'key' => $key],
                ['value' => $value]
            );
        }
    }
}
