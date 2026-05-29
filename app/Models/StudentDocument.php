<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDocument extends Model
{
    protected $fillable = ['student_id', 'label', 'path', 'mime_type', 'size_bytes', 'uploaded_by'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
