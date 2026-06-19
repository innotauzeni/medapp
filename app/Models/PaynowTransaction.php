<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaynowTransaction extends Model
{
    protected $fillable = [
        'booking_id',
        'payment_channel_id',
        'book_payment_id',
        'uuid',
        'pollurl',
        'status',
        'amount',
        'currency',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function paymentChannel(): BelongsTo
    {
        return $this->belongsTo(PaymentChannel::class);
    }

    public function bookPayment(): BelongsTo
    {
        return $this->belongsTo(BookPayment::class);
    }
}
