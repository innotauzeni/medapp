<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\Enrolment;
use App\Repositories\Contracts\EnrolmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrolmentService
{
    public function __construct(private readonly EnrolmentRepositoryInterface $enrolments) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->enrolments->paginate($perPage, $filters);
    }

    public function get(int $id): Enrolment
    {
        /** @var Enrolment $enrolment */
        $enrolment = $this->enrolments->findOrFail($id);
        $enrolment->load(['student', 'course.modules', 'schedule.location', 'attendances', 'assessments', 'certificate']);
        return $enrolment;
    }

    public function create(array $data): Enrolment
    {
        $data['enrolled_on'] = $data['enrolled_on'] ?? now()->toDateString();
        $data['created_by']  = Auth::id();
        /** @var Enrolment $enrolment */
        $enrolment = $this->enrolments->create($data);
        return $enrolment->load(['student', 'course', 'schedule']);
    }

    public function update(int $id, array $data): Enrolment
    {
        /** @var Enrolment $enrolment */
        $enrolment = $this->enrolments->update($id, $data);
        return $enrolment->load(['student', 'course', 'schedule']);
    }

    public function recordAttendance(int $enrolmentId, array $data): Attendance
    {
        $enrolment = $this->enrolments->findOrFail($enrolmentId);
        return Attendance::updateOrCreate(
            ['enrolment_id' => $enrolment->id, 'date' => $data['date']],
            [
                'status'      => $data['status'],
                'remarks'     => $data['remarks'] ?? null,
                'recorded_by' => Auth::id(),
            ]
        );
    }

    public function recordAssessment(int $enrolmentId, array $data): Assessment
    {
        $enrolment = $this->enrolments->findOrFail($enrolmentId);

        return DB::transaction(function () use ($enrolment, $data) {
            $assessment = Assessment::create([
                'enrolment_id'     => $enrolment->id,
                'course_module_id' => $data['course_module_id'] ?? null,
                'title'            => $data['title'],
                'type'             => $data['type'] ?? 'theory',
                'score'            => $data['score'] ?? null,
                'max_score'        => $data['max_score'] ?? 100,
                'outcome'          => $this->deriveOutcome($enrolment->course?->passing_score ?? 50, $data),
                'trainer_comments' => $data['trainer_comments'] ?? null,
                'assessed_on'      => $data['assessed_on'] ?? now()->toDateString(),
                'assessed_by'      => Auth::id(),
            ]);

            $this->recalculateFinal($enrolment);

            return $assessment;
        });
    }

    public function deleteAssessment(int $assessmentId): bool
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $enrolment  = $assessment->enrolment;
        $deleted    = (bool) $assessment->delete();
        if ($enrolment) {
            $this->recalculateFinal($enrolment);
        }
        return $deleted;
    }

    public function complete(int $id, ?float $finalScore = null, ?string $comments = null): Enrolment
    {
        return $this->update($id, [
            'status'           => 'completed',
            'completed_on'     => now()->toDateString(),
            'final_score'      => $finalScore,
            'trainer_comments' => $comments,
        ]);
    }

    private function deriveOutcome(int $passingScore, array $data): string
    {
        if (!isset($data['score'])) {
            return 'pending';
        }
        $score    = (float) $data['score'];
        $maxScore = (float) ($data['max_score'] ?? 100);
        if ($maxScore <= 0) {
            return 'pending';
        }
        $pct = ($score / $maxScore) * 100;
        return $pct >= $passingScore ? 'pass' : 'fail';
    }

    private function recalculateFinal(Enrolment $enrolment): void
    {
        $assessments = $enrolment->assessments()->whereNotNull('score')->get();
        if ($assessments->isEmpty()) {
            $enrolment->update(['final_score' => null]);
            return;
        }
        $totalPct = $assessments->avg(fn (Assessment $a) =>
            $a->max_score > 0 ? ((float) $a->score / (float) $a->max_score) * 100 : 0);

        $enrolment->update(['final_score' => round($totalPct, 2)]);
    }
}
