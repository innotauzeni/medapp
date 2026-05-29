<?php

namespace App\Services\Cart;

use App\Models\Course;
use App\Models\CourseSchedule;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'er_cart';

    /** @return array<int, array{course_id:int, schedule_id:?int, quantity:int}> */
    public function items(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return array_sum(array_map(fn ($i) => (int) ($i['quantity'] ?? 1), $this->items()));
    }

    public function add(int $courseId, ?int $scheduleId = null, int $quantity = 1): void
    {
        $items = $this->items();
        $key = $this->key($courseId, $scheduleId);
        if (isset($items[$key])) {
            $items[$key]['quantity'] = (int) $items[$key]['quantity'] + $quantity;
        } else {
            $items[$key] = [
                'course_id'   => $courseId,
                'schedule_id' => $scheduleId,
                'quantity'    => max(1, $quantity),
            ];
        }
        Session::put(self::SESSION_KEY, $items);
    }

    public function update(string $key, int $quantity): void
    {
        $items = $this->items();
        if (!isset($items[$key])) return;
        if ($quantity <= 0) {
            unset($items[$key]);
        } else {
            $items[$key]['quantity'] = $quantity;
        }
        Session::put(self::SESSION_KEY, $items);
    }

    public function remove(string $key): void
    {
        $items = $this->items();
        unset($items[$key]);
        Session::put(self::SESSION_KEY, $items);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Replace the cart with a normalised list (for localStorage sync from client).
     * @param array<int, array{course_id:int|string, schedule_id?:int|string|null, quantity?:int}> $incoming
     */
    public function sync(array $incoming): void
    {
        $items = [];
        $courseIds = collect($incoming)->pluck('course_id')->map(fn ($v) => (int) $v)->filter()->unique();
        $validCourses = Course::whereIn('id', $courseIds)->pluck('id')->all();

        $scheduleIds = collect($incoming)->pluck('schedule_id')->map(fn ($v) => (int) $v)->filter()->unique();
        $validSchedules = CourseSchedule::whereIn('id', $scheduleIds)->pluck('id')->all();

        foreach ($incoming as $row) {
            $courseId   = (int) ($row['course_id'] ?? 0);
            $scheduleId = isset($row['schedule_id']) ? (int) $row['schedule_id'] : null;
            $qty        = max(1, (int) ($row['quantity'] ?? 1));

            if (!in_array($courseId, $validCourses, true)) continue;
            if ($scheduleId && !in_array($scheduleId, $validSchedules, true)) {
                $scheduleId = null;
            }

            $key = $this->key($courseId, $scheduleId);
            $items[$key] = [
                'course_id'   => $courseId,
                'schedule_id' => $scheduleId ?: null,
                'quantity'    => $qty,
            ];
        }

        Session::put(self::SESSION_KEY, $items);
    }

    /** Resolve cart entries to full Course / Schedule models for display. */
    public function detailed(): array
    {
        $items   = $this->items();
        if (empty($items)) return [];

        $courses   = Course::whereIn('id', array_column($items, 'course_id'))->get()->keyBy('id');
        $schedules = CourseSchedule::with(['location', 'leadTrainer'])
            ->whereIn('id', array_filter(array_column($items, 'schedule_id')))
            ->get()->keyBy('id');

        $out = [];
        foreach ($items as $key => $i) {
            $course = $courses->get($i['course_id']);
            if (!$course) continue;
            $out[] = [
                'key'      => $key,
                'course'   => $course,
                'schedule' => $i['schedule_id'] ? $schedules->get($i['schedule_id']) : null,
                'quantity' => (int) $i['quantity'],
                'subtotal' => (float) $course->fee * (int) $i['quantity'],
            ];
        }
        return $out;
    }

    public function total(): float
    {
        return collect($this->detailed())->sum('subtotal');
    }

    private function key(int $courseId, ?int $scheduleId): string
    {
        return $courseId . ':' . ($scheduleId ?? 'none');
    }
}
