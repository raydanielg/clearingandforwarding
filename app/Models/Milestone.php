<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = ['shipment_file_id', 'stage', 'happened_at', 'recorded_by', 'remarks'];

    protected function casts(): array
    {
        return ['happened_at' => 'datetime'];
    }

    public function shipmentFile()
    {
        return $this->belongsTo(ShipmentFile::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
