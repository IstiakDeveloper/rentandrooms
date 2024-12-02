<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'payment_method',
        'amount',
        'transaction_id',
        'status'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}