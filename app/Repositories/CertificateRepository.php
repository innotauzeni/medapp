<?php

namespace App\Repositories;

use App\Models\Certificate;
use App\Repositories\Contracts\CertificateRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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

    /**
     * Reserve and return the next incremental certificate number.
     *
     * Format: a 2-letter prefix followed by a zero-padded 6-digit counter
     * (e.g. AA000001, AA000002). When the counter passes 999999 the prefix
     * rolls over to the next combination and the counter resets, so the
     * number after AA999999 is AB000000 (and after ZZ999999 wraps to AA000000).
     *
     * The whole read-increment-write happens inside a transaction with a row
     * lock so concurrent issuances can never produce a duplicate number.
     */
    public function generateCertificateNumber(): string
    {
        return DB::transaction(function () {
            $seq = DB::table('certificate_sequences')->lockForUpdate()->first();

            if (!$seq) {
                $id  = DB::table('certificate_sequences')->insertGetId([
                    'prefix'         => 'AA',
                    'current_number' => 0,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
                $seq = DB::table('certificate_sequences')->lockForUpdate()->find($id);
            }

            $next   = $seq->current_number + 1;
            $prefix = $seq->prefix;

            if ($next > 999999) {
                $prefix = $this->incrementPrefix($prefix);
                $next   = 0;
            }

            DB::table('certificate_sequences')->where('id', $seq->id)->update([
                'prefix'         => $prefix,
                'current_number' => $next,
                'updated_at'     => now(),
            ]);

            return $prefix . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Advance a two-letter prefix: AA -> AB ... AZ -> BA ... ZZ -> AA.
     */
    private function incrementPrefix(string $prefix): string
    {
        $first  = $prefix[0];
        $second = $prefix[1];

        if ($second === 'Z') {
            $second = 'A';
            $first  = $first === 'Z' ? 'A' : chr(ord($first) + 1);
        } else {
            $second = chr(ord($second) + 1);
        }

        return $first . $second;
    }

    public function generateVerificationCode(): string
    {
        do {
            $code = Str::upper(Str::random(16));
        } while (Certificate::withTrashed()->where('verification_code', $code)->exists());

        return $code;
    }
}
