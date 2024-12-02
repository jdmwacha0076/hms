<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedContract extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'tenant_id', 'house_id', 'file_path'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function house()
    {
        return $this->belongsTo(House::class);
    }
}
