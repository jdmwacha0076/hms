<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'house_id',
        'room_id',
        'start_date',
        'end_date',
        'contract_interval',
        'amount_paid',
        'amount_remaining',
        'total',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function house()
    {
        return $this->belongsTo(House::class);
    }
}
