<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileCharge extends Model
{
    protected $fillable = ['shipment_file_id', 'charge_item_id', 'description', 'amount', 'currency', 'is_billable'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'is_billable' => 'boolean'];
    }

    public function shipmentFile()
    {
        return $this->belongsTo(ShipmentFile::class);
    }

    public function chargeItem()
    {
        return $this->belongsTo(ChargeItem::class);
    }
}
