<?php

namespace App\Repositories;

use App\Models\Certificate;
use App\Repositories\Contracts\CertificateRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CertificateRepository extends BaseRepository implements CertificateRepositoryInterface
{
    protected array $searchable = ['certificate_number', 'verification_code'];

    public function __construct(Certificate $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        $query = parent::applyFilters($query, $filters);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        return $query->with(['student', 'course', 'issuer'])->latest('id');
    }

    public function findByNumber(string $number): ?Certificate
    {
        return $this->findBy('certificate_number', $number);
    }

    public function findByVerificationCode(string $code): ?Certificate
    {
        return $this->findBy('verification_code', $code);
    }

    public function generateCertificateNumber(): string
    {
        $year = now()->format('Y');
        $last = Certificate::withTrashed()
            ->where('certificate_number', 'like', "ERM-CERT-{$year}-%")
            ->orderByDesc('id')
            ->value('certificate_number');

        $seq = $last ? ((int) substr($last, -6)) + 1 : 1;
        return sprintf('ERM-CERT-%s-%06d', $year, $seq);
    }

    public function generateVerificationCode(): string
    {
        do {
            $code = Str::upper(Str::random(16));
        } while (Certificate::withTrashed()->where('verification_code', $code)->exists());

        return $code;
    }
}
