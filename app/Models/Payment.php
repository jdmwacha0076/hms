<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'amount',
        'amount_remaining',
        'payment_date',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
