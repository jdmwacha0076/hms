<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = ['tenant_name', 'phone_number', 'business_name', 'id_type', 'id_number'];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function uploadedContracts()
    {
        return $this->hasMany(UploadedContract::class);
    }
}
