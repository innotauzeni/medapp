<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseSchedule;
use App\Models\Location;
use App\Models\Student;
use App\Models\Trainer;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $first = CourseCategory::firstOrCreate(['slug' => 'first-aid'], ['name' => 'First Aid', 'description' => 'Basic and advanced first response.']);
        $sheq  = CourseCategory::firstOrCreate(['slug' => 'sheq'],      ['name' => 'SHEQ',      'description' => 'Safety, Health, Environment, Quality.']);
        $bls   = CourseCategory::firstOrCreate(['slug' => 'bls'],       ['name' => 'Basic Life Support', 'description' => 'CPR, AED, airway management.']);

        $hq      = Location::firstOrCreate(['name' => 'ER Medics HQ'],     ['address_line1' => '12 Response Road', 'city' => 'Johannesburg', 'province' => 'Gauteng']);
        $capeTwn = Location::firstOrCreate(['name' => 'Cape Town Branch'], ['address_line1' => '7 Harbour Lane',    'city' => 'Cape Town',    'province' => 'Western Cape']);

        $bfa = Course::firstOrCreate(
            ['code' => 'BFA-26A'],
            ['title' => 'Basic First Aid Level 1', 'category_id' => $first->id, 'description' => '2-day introduction to first aid.', 'duration_hours' => 16, 'passing_score' => 50, 'fee' => 1450, 'is_active' => true]
        );
        $blsCourse = Course::firstOrCreate(
            ['code' => 'BLS-26A'],
            ['title' => 'Basic Life Support (CPR + AED)', 'category_id' => $bls->id, 'description' => 'CPR + AED.', 'duration_hours' => 8, 'passing_score' => 60, 'fee' => 950, 'is_active' => true]
        );
        $sheqCourse = Course::firstOrCreate(
            ['code' => 'SHEQ-26A'],
            ['title' => 'SHEQ Awareness for Supervisors', 'category_id' => $sheq->id, 'description' => 'SHEQ essentials.', 'duration_hours' => 24, 'passing_score' => 60, 'fee' => 2200, 'is_active' => true]
        );

        $sarah = Trainer::firstOrCreate(
            ['email' => 'sarah@ermedics.local'],
            ['first_name' => 'Sarah', 'last_name' => 'Naidoo',  'phone' => '+27 82 111 2222', 'specialty' => 'Pre-hospital emergency care', 'qualifications' => 'ECP, ALS', 'is_active' => true]
        );
        $thabo = Trainer::firstOrCreate(
            ['email' => 'thabo@ermedics.local'],
            ['first_name' => 'Thabo', 'last_name' => 'Mokoena', 'phone' => '+27 82 333 4444', 'specialty' => 'SHEQ',                        'qualifications' => 'SAMTRAC, NEBOSH IGC', 'is_active' => true]
        );

        // Demo schedules so the enrolment "Schedule" dropdown is populated.
        $schedules = [
            ['course' => $bfa,        'location' => $hq,      'trainer' => $sarah, 'start' => '+10 days', 'end' => '+11 days', 'capacity' => 20, 'status' => 'scheduled'],
            ['course' => $bfa,        'location' => $capeTwn, 'trainer' => $sarah, 'start' => '+25 days', 'end' => '+26 days', 'capacity' => 16, 'status' => 'scheduled'],
            ['course' => $blsCourse,  'location' => $hq,      'trainer' => $sarah, 'start' => '+7 days',  'end' => '+7 days',  'capacity' => 24, 'status' => 'scheduled'],
            ['course' => $blsCourse,  'location' => $capeTwn, 'trainer' => $sarah, 'start' => '+30 days', 'end' => '+30 days', 'capacity' => 18, 'status' => 'scheduled'],
            ['course' => $sheqCourse, 'location' => $hq,      'trainer' => $thabo, 'start' => '+14 days', 'end' => '+16 days', 'capacity' => 15, 'status' => 'scheduled'],
            ['course' => $sheqCourse, 'location' => $hq,      'trainer' => $thabo, 'start' => '-30 days', 'end' => '-28 days', 'capacity' => 15, 'status' => 'completed'],
        ];

        foreach ($schedules as $s) {
            CourseSchedule::firstOrCreate(
                [
                    'course_id'  => $s['course']->id,
                    'start_date' => now()->modify($s['start'])->toDateString(),
                    'end_date'   => now()->modify($s['end'])->toDateString(),
                ],
                [
                    'location_id'      => $s['location']->id,
                    'lead_trainer_id'  => $s['trainer']->id,
                    'capacity'         => $s['capacity'],
                    'status'           => $s['status'],
                ]
            );
        }

        if (Student::count() === 0) {
            $samples = [
                ['Nomvula', 'Khumalo', 'nomvula.k@example.com'],
                ['Pieter',  'van Wyk', 'pieter.vw@example.com'],
                ['Aisha',   'Patel',   'aisha.p@example.com'],
            ];
            foreach ($samples as $i => [$f, $l, $e]) {
                Student::create([
                    'student_number' => sprintf('ERM-26-%05d', $i + 1),
                    'first_name'     => $f,
                    'last_name'      => $l,
                    'email'          => $e,
                    'phone'          => '+27 71 ' . str_pad((string) random_int(0, 9999999), 7, '0'),
                    'id_type'        => 'id',
                    'country'        => 'South Africa',
                    'status'         => 'active',
                ]);
            }
        }
    }
}
