<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'certificate_number', 'enrolment_id', 'student_id', 'course_id',
        'issued_by', 'issued_at', 'expires_at',
        'score', 'grade', 'verification_code',
        'status', 'revoked_reason', 'pdf_path',
    ];

    protected $casts = [
        'issued_at'  => 'date',
        'expires_at' => 'date',
        'score'      => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrolment(): BelongsTo
    {
        return $this->belongsTo(Enrolment::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(CertificateVerification::class);
    }

    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
