<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManualTransaction extends Model
{
    protected $fillable = [
        "booking_id",
        "payment_channel_id",
        "amount",
        "currency",
        "year",
        "reference",
    ];

    protected $casts = [
        "amount" => "decimal:2",
        "year" => "integer",
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function paymentChannel(): BelongsTo
    {
        return $this->belongsTo(PaymentChannel::class);
    }
}
