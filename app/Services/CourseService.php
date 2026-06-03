<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            $this->applyImage($data);

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

            /** @var Course $existing */
            $existing = $this->courses->findOrFail($id);
            $this->applyImage($data, $existing);

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

    /**
     * Resolve the feature-image upload into a stored path.
     *
     * Stores a newly uploaded file on the public disk and sets `image_path`,
     * deleting any previous file. Honours a `remove_image` flag on update.
     * The transient `image` / `remove_image` keys are stripped so they never
     * reach the repository.
     */
    private function applyImage(array &$data, ?Course $existing = null): void
    {
        $remove = (bool) ($data['remove_image'] ?? false);
        $file   = $data['image'] ?? null;
        unset($data['image'], $data['remove_image']);

        if ($file instanceof UploadedFile) {
            if ($existing?->image_path) {
                Storage::disk('public')->delete($existing->image_path);
            }
            $data['image_path'] = $file->store('courses', 'public');
            return;
        }

        if ($remove) {
            if ($existing?->image_path) {
                Storage::disk('public')->delete($existing->image_path);
            }
            $data['image_path'] = null;
        }
    }

    private function generateCode(string $title): string
    {
        $base = Str::upper(Str::limit(Str::slug($title, ''), 6, ''));
        return $base . '-' . now()->format('y') . Str::upper(Str::random(3));
    }
}
