<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'phone', 'license_no', 'license_expiry', 'truck_id', 'is_active'];

    protected function casts(): array
    {
        return ['license_expiry' => 'date', 'is_active' => 'boolean'];
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
