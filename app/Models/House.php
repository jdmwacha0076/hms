<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Supervisor;

class House extends Model
{
    use HasFactory;

    protected $table = 'houses';

    protected $fillable = [
        'house_name',
        'house_location',
        'street_name',
        'house_owner',
        'plot_number',
        'phone_number',
        'supervisor_id'
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function uploadedContracts()
    {
        return $this->hasMany(UploadedContract::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id');
    }

    public function houses()
    {
        return $this->hasMany(House::class, 'supervisor_id');
    }
}
