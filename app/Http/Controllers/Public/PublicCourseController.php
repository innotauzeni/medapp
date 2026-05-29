<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCourseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::with('category', 'schedules.location')->where('is_active', true);

        if ($request->filled('category')) {
            $cat = CourseCategory::where('slug', $request->string('category'))->first();
            if ($cat) {
                $query->where('category_id', $cat->id);
            }
        }
        if ($request->filled('q')) {
            $q = (string) $request->string('q');
            $query->where(function ($x) use ($q) {
                $x->where('title', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%")
                  ->orWhere('code', 'like', "%{$q}%");
            });
        }

        return view('public.courses', [
            'courses'    => $query->orderBy('title')->get(),
            'categories' => CourseCategory::where('is_active', true)->orderBy('name')->get(),
            'activeCat'  => $request->input('category'),
            'q'          => $request->input('q'),
        ]);
    }

    public function show(Course $course): View
    {
        abort_unless($course->is_active, 404);
        $course->load(['category', 'modules', 'schedules.location', 'schedules.leadTrainer']);

        $related = Course::where('is_active', true)
            ->where('id', '!=', $course->id)
            ->where(function ($q) use ($course) {
                $q->where('category_id', $course->category_id)
                  ->orWhere('category_id', null);
            })
            ->limit(3)->get();

        return view('public.course', compact('course', 'related'));
    }
}
