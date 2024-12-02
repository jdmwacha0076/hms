<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_name', 'rent', 'house_id'];

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function uploadedContracts()
    {
        return $this->hasMany(UploadedContract::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }
}
