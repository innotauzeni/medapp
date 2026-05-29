<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label', 'description'];

    /** Cast `value` based on the `type` column when read. */
    public function getCastValueAttribute(): mixed
    {
        return match ($this->type) {
            'int'  => (int) $this->value,
            'bool' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => $this->value ? json_decode($this->value, true) : null,
            default => (string) $this->value,
        };
    }
}
