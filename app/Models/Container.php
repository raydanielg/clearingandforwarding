<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $fillable = ['consignment_id', 'container_no', 'size', 'seal_no', 'status', 'empty_return_due'];

    protected function casts(): array
    {
        return ['empty_return_due' => 'date'];
    }

    public function consignment()
    {
        return $this->belongsTo(Consignment::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
