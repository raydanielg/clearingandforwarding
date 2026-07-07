<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PettyCash extends Model
{
    use SoftDeletes;

    protected $table = 'petty_cash';

    protected $fillable = ['date', 'description', 'amount', 'type', 'shipment_file_id', 'recorded_by', 'approved_by'];

    protected function casts(): array
    {
        return ['date' => 'date', 'amount' => 'decimal:2'];
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
