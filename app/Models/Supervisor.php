<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supervisor extends Model
{
    use HasFactory;

    protected $table = 'supervisors';

    protected $fillable = [
        'supervisor_name',
        'phone_number',
    ];

    public function houses()
    {
        return $this->hasMany(House::class, 'supervisor_id');
    }
}
