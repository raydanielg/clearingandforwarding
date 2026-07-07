<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    use SoftDeletes;

    protected $fillable = ['plate_no', 'trailer_no', 'capacity_tons', 'transporter_id', 'is_active'];

    protected function casts(): array
    {
        return ['capacity_tons' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
