<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentChannel extends Model
{
    protected $fillable = ["name", "slug", "is_active", "description"];

    protected $casts = ["is_active" => "boolean"];

    public function parameters(): HasMany
    {
        return $this->hasMany(PaymentChannelParameter::class);
    }

    public function bookPayments(): HasMany
    {
        return $this->hasMany(BookPayment::class);
    }

    public function manualTransactions(): HasMany
    {
        return $this->hasMany(ManualTransaction::class);
    }

    public function paynowTransactions(): HasMany
    {
        return $this->hasMany(PaynowTransaction::class);
    }
}
