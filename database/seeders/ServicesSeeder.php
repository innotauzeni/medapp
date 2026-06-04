<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        if (Service::count() > 0) return;

        $services = [
            ['bi-clipboard-data',          'Medical Equipment Consulting',        'Assess medical equipment needs, recommend suitable products, and assist with procurement and setup.'],
            ['bi-tools',                   'Medical Equipment Maintenance',       'Maintenance and servicing packages to keep your medical equipment in optimal condition.'],
            ['bi-box-seam',                'Medical Equipment Rental',            'Short-term or specialized equipment for events and projects — flexible rental services.'],
            ['bi-mortarboard',             'Medical Equipment Training',          'Training and support on how to effectively and safely use the medical equipment we supply.'],
            ['bi-sliders',                 'Medical Equipment Customization',     'Tailoring medical equipment to your specific needs, ensuring optimal performance and usability.'],
            ['bi-shield-fill-exclamation', 'Emergency Preparedness Consultation', "Assessments of your facility's emergency preparedness with comprehensive plans and protocols."],
        ];

        foreach ($services as $i => [$icon, $title, $desc]) {
            Service::create([
                'icon'        => $icon,
                'title'       => $title,
                'description' => $desc,
                'order_index' => ($i + 1) * 10,
                'is_active'   => true,
            ]);
        }
    }
}
