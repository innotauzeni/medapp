<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Enrolment;
use App\Models\HeroSlide;
use App\Models\Service;
use App\Models\Student;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $slides = HeroSlide::where('is_active', true)
            ->orderBy('order_index')->get();

        $courses = Course::with('category')->where('is_active', true)
            ->orderBy('title')->limit(6)->get();

        $categories = CourseCategory::where('is_active', true)->orderBy('name')->get();

        $services = Service::where('is_active', true)->orderBy('order_index')->get();

        $stats = [
            'students_trained'    => max(Student::count(), 0),
            'courses_offered'     => Course::where('is_active', true)->count(),
            'certificates_issued' => Certificate::where('status', 'issued')->count(),
            'completion_rate'     => $this->completionRate(),
        ];

        return view('public.home', compact('slides', 'courses', 'categories', 'services', 'stats'));
    }

    private function completionRate(): int
    {
        $total     = Enrolment::count();
        $completed = Enrolment::where('status', 'completed')->count();
        return $total > 0 ? (int) round($completed / $total * 100) : 0;
    }
}
