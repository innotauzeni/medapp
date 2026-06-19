<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookPayment extends Model
{
    protected $fillable = [
        "booking_id",
        "payment_channel_id",
        "total_ammount",
        "uuid",
        "pollurl",
        "currency",
        "description",
        "status",
    ];

    protected $casts = [
        "total_ammount" => "decimal:2",
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function paymentChannel(): BelongsTo
    {
        return $this->belongsTo(PaymentChannel::class);
    }

    public function receipts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookReceipt::class);
    }
}
