<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseService
{
    public function __construct(private readonly CourseRepositoryInterface $courses) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->courses->paginate($perPage, $filters);
    }

    public function get(int $id): Course
    {
        /** @var Course $course */
        $course = $this->courses->findOrFail($id);
        $course->load(['category', 'modules', 'schedules.location', 'schedules.leadTrainer']);
        return $course;
    }

    public function create(array $data): Course
    {
        return DB::transaction(function () use ($data) {
            $data['code'] = $data['code'] ?? $this->generateCode($data['title']);
            $modules = $data['modules'] ?? [];
            unset($data['modules']);

            /** @var Course $course */
            $course = $this->courses->create($data);

            foreach ($modules as $i => $m) {
                if (empty($m['title'])) continue;
                $course->modules()->create([
                    'title'          => $m['title'],
                    'description'    => $m['description'] ?? null,
                    'duration_hours' => (int) ($m['duration_hours'] ?? 1),
                    'order_index'    => $i,
                ]);
            }

            return $course->load(['category', 'modules']);
        });
    }

    public function update(int $id, array $data): Course
    {
        return DB::transaction(function () use ($id, $data) {
            $modules = $data['modules'] ?? null;
            unset($data['modules']);

            /** @var Course $course */
            $course = $this->courses->update($id, $data);

            if (is_array($modules)) {
                $course->modules()->delete();
                foreach ($modules as $i => $m) {
                    if (empty($m['title'])) continue;
                    $course->modules()->create([
                        'title'          => $m['title'],
                        'description'    => $m['description'] ?? null,
                        'duration_hours' => (int) ($m['duration_hours'] ?? 1),
                        'order_index'    => $i,
                    ]);
                }
            }

            return $course->load(['category', 'modules']);
        });
    }

    public function delete(int $id): bool
    {
        return $this->courses->delete($id);
    }

    private function generateCode(string $title): string
    {
        $base = Str::upper(Str::limit(Str::slug($title, ''), 6, ''));
        return $base . '-' . now()->format('y') . Str::upper(Str::random(3));
    }
}
