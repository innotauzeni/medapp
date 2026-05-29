<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Branding
            ['key' => 'site_name',           'value' => 'ER Medics', 'type' => 'string', 'group' => 'branding',  'label' => 'Site name',         'description' => 'Shown in the navbar and emails'],
            ['key' => 'site_tagline',        'value' => 'Emergency Medical Training, SHEQ &amp; Equipment', 'type' => 'string', 'group' => 'branding', 'label' => 'Tagline'],

            // Currency
            ['key' => 'currency_code',          'value' => 'USD', 'type' => 'string', 'group' => 'currency', 'label' => 'Currency code',          'description' => 'ISO 4217 (e.g. ZAR, USD, GBP, EUR, ZWL)'],
            ['key' => 'currency_symbol',        'value' => 'USD',   'type' => 'string', 'group' => 'currency', 'label' => 'Currency symbol',        'description' => 'Symbol shown in prices (e.g. R, $, £, €)'],
            ['key' => 'currency_symbol_position','value'=> 'before', 'type' => 'string', 'group' => 'currency', 'label' => 'Symbol position',       'description' => 'before or after the amount'],

            // Contact
            ['key' => 'phone_primary',   'value' => '0784701050', 'type' => 'string', 'group' => 'contact', 'label' => 'Primary phone'],
            ['key' => 'phone_secondary', 'value' => '0773295850', 'type' => 'string', 'group' => 'contact', 'label' => 'Secondary phone'],
            ['key' => 'email',           'value' => 'info@ermedics.local', 'type' => 'string', 'group' => 'contact', 'label' => 'Public email'],
            ['key' => 'address',         'value' => '', 'type' => 'string', 'group' => 'contact', 'label' => 'Physical address'],

            // Social
            ['key' => 'facebook_url',  'value' => '', 'type' => 'string', 'group' => 'social', 'label' => 'Facebook URL'],
            ['key' => 'instagram_url', 'value' => '', 'type' => 'string', 'group' => 'social', 'label' => 'Instagram URL'],
            ['key' => 'linkedin_url',  'value' => '', 'type' => 'string', 'group' => 'social', 'label' => 'LinkedIn URL'],
            ['key' => 'whatsapp_url',  'value' => '', 'type' => 'string', 'group' => 'social', 'label' => 'WhatsApp link'],
        ];

        foreach ($defaults as $row) {
            SiteSetting::firstOrCreate(['key' => $row['key']], $row);
        }
    }
}
