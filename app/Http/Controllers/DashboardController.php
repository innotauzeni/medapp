<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'students'          => Student::where('status', 'active')->count(),
            'courses'           => Course::where('is_active', true)->count(),
            'active_enrolments' => Enrolment::whereIn('status', ['enrolled', 'in_progress'])->count(),
            'certificates'      => Certificate::where('status', 'issued')->count(),
        ];

        $since30 = now()->subDays(30);
        $deltas = [
            'students'     => Student::where('created_at', '>=', $since30)->count(),
            'enrolments'   => Enrolment::where('created_at', '>=', $since30)->count(),
            'certificates' => Certificate::where('issued_at', '>=', $since30->toDateString())->count(),
            'completions'  => Enrolment::where('status', 'completed')->where('completed_on', '>=', $since30->toDateString())->count(),
        ];

        $months = collect(range(5, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));
        $trend = [
            'labels'       => $months->map(fn (Carbon $m) => $m->format('M Y'))->all(),
            'enrolments'   => $months->map(fn (Carbon $m) =>
                Enrolment::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count()
            )->all(),
            'certificates' => $months->map(fn (Carbon $m) =>
                Certificate::whereYear('issued_at', $m->year)->whereMonth('issued_at', $m->month)->count()
            )->all(),
        ];

        $recentCertificates = Certificate::with(['student', 'course'])
            ->where('status', 'issued')
            ->latest('issued_at')
            ->limit(6)
            ->get();

        $recentEnrolments = Enrolment::with(['student', 'course'])
            ->latest('id')
            ->limit(6)
            ->get();

        return view('dashboard', compact('stats', 'deltas', 'trend', 'recentCertificates', 'recentEnrolments'));
    }
}
