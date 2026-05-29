<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_number', 'first_name', 'last_name', 'email', 'phone',
        'date_of_birth', 'gender', 'id_type', 'id_number', 'nationality',
        'address_line1', 'city', 'province', 'country',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
        'employer', 'job_title', 'profile_photo_path', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function fullName(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->first_name} {$this->last_name}"));
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
