<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $range = $request->input('range', '90d');
        $since = match ($range) {
            '7d'   => now()->subDays(7),
            '30d'  => now()->subDays(30),
            '180d' => now()->subDays(180),
            '365d' => now()->subDays(365),
            default => now()->subDays(90),
        };

        $registrations = Student::where('created_at', '>=', $since)->count();
        $completions   = Enrolment::where('status', 'completed')->where('completed_on', '>=', $since)->count();
        $issued        = Certificate::where('status', 'issued')->where('issued_at', '>=', $since)->count();

        $byCourse = Enrolment::selectRaw('course_id, count(*) as total, sum(case when status=\'completed\' then 1 else 0 end) as completed')
            ->where('created_at', '>=', $since)
            ->groupBy('course_id')
            ->with('course:id,code,title')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $latestCertificates = Certificate::with(['student', 'course'])
            ->where('issued_at', '>=', $since)
            ->latest('issued_at')
            ->limit(20)
            ->get();

        return view('reports.index', [
            'range'              => $range,
            'since'              => $since,
            'registrations'      => $registrations,
            'completions'        => $completions,
            'issued'             => $issued,
            'byCourse'           => $byCourse,
            'latestCertificates' => $latestCertificates,
            'totals'             => [
                'students'     => Student::count(),
                'courses'      => Course::count(),
                'enrolments'   => Enrolment::count(),
                'certificates' => Certificate::count(),
            ],
        ]);
    }
}
