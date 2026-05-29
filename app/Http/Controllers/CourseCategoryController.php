<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\Request;

class CourseCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('categories.view');
        $paginator = CourseCategory::withCount('courses')->orderBy('name')->paginate(20);
        return view('categories.index', compact('paginator'));
    }

    public function create(): View
    {
        $this->authorize('categories.create');
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('categories.create');
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:120', 'unique:course_categories,name'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active'   => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        CourseCategory::create($data);
        return redirect()->route('categories.index')->with('status', 'Category created.');
    }

    public function edit(CourseCategory $category): View
    {
        $this->authorize('categories.update');
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, CourseCategory $category): RedirectResponse
    {
        $this->authorize('categories.update');
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:120', Rule::unique('course_categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active'   => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $category->update($data);
        return redirect()->route('categories.index')->with('status', 'Category updated.');
    }

    public function destroy(CourseCategory $category): RedirectResponse
    {
        $this->authorize('categories.delete');
        if ($category->courses()->exists()) {
            return back()->with('error', 'Cannot delete a category that has courses. Reassign them first.');
        }
        $category->delete();
        return back()->with('status', 'Category deleted.');
    }
}
