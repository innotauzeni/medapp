<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlidesSeeder extends Seeder
{
    public function run(): void
    {
        if (HeroSlide::count() > 0) return;

        $slides = [
            [
                'badge'    => 'Emergency Medical Training',
                'title'    => 'Reliable medical equipment <br>and emergency training',
                'subtitle' => 'We empower individuals and organisations to respond confidently, safely, and professionally in emergencies — through training, mentorship, SHEQ consultancy, and reliable medical equipment.',
                'image_url'=> 'https://images.unsplash.com/photo-1612277795421-9bc7706a4a34?auto=format&fit=crop&w=1600&q=80',
                'cta_label'=> 'Browse courses',
                'cta_url'  => '/courses',
                'secondary_cta_label' => 'Our services',
                'secondary_cta_url'   => '#services',
                'order_index' => 10,
                'is_active'   => true,
            ],
            [
                'badge'    => 'First Responder Programme',
                'title'    => 'Train for the moments<br>that matter most',
                'subtitle' => 'Industry-recognised Basic Life Support, First Aid, and Emergency Care courses delivered by experienced paramedics across South Africa.',
                'image_url'=> 'https://images.unsplash.com/photo-1581595220892-b0739db3ba8c?auto=format&fit=crop&w=1600&q=80',
                'cta_label'=> 'Find a course',
                'cta_url'  => '/courses',
                'secondary_cta_label' => 'Verify a certificate',
                'secondary_cta_url'   => '/verify',
                'order_index' => 20,
                'is_active'   => true,
            ],
            [
                'badge'    => 'Equipment &amp; Consulting',
                'title'    => 'Equipped for life-saving<br>operational readiness',
                'subtitle' => 'From procurement and maintenance to customised training — we keep your team ready for any emergency. SHEQ consulting for organisations across diverse sectors.',
                'image_url'=> 'https://images.unsplash.com/photo-1530026405186-ed1f139313f8?auto=format&fit=crop&w=1600&q=80',
                'cta_label'=> 'Contact our team',
                'cta_url'  => '#contact',
                'secondary_cta_label' => 'Browse courses',
                'secondary_cta_url'   => '/courses',
                'order_index' => 30,
                'is_active'   => true,
            ],
        ];

        foreach ($slides as $s) HeroSlide::create($s);
    }
}
