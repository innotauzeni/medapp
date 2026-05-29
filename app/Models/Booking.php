<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_code', 'first_name', 'last_name', 'email', 'phone',
        'id_type', 'id_number', 'organization', 'city', 'notes',
        'status', 'assigned_to', 'contacted_at', 'confirmed_at',
        'source', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function fullName(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->first_name} {$this->last_name}"));
    }

    public function totalAmount(): Attribute
    {
        return Attribute::get(fn () =>
            $this->items->sum(fn (BookingItem $i) => (float) $i->unit_price * (int) $i->quantity)
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(BookingStatusLog::class)->latest();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
