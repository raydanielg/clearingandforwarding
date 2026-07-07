<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shipment_file_id', 'do_no', 'shipping_line_id', 'issued_at', 'expiry_date', 'deposit_amount', 'deposit_refunded'
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'expiry_date' => 'date',
            'deposit_amount' => 'decimal:2',
            'deposit_refunded' => 'boolean',
        ];
    }

    public function shipmentFile()
    {
        return $this->belongsTo(ShipmentFile::class);
    }

    public function shippingLine()
    {
        return $this->belongsTo(ShippingLine::class);
    }
}
