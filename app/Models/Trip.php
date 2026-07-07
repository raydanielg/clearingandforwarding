<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shipment_file_id', 'container_id', 'truck_id', 'driver_id', 'origin', 'destination',
        'dispatched_at', 'delivered_at', 'status', 'pod_document_id', 'transport_cost', 'notes'
    ];

    protected function casts(): array
    {
        return [
            'dispatched_at' => 'datetime',
            'delivered_at' => 'datetime',
            'transport_cost' => 'decimal:2',
        ];
    }

    public function shipmentFile()
    {
        return $this->belongsTo(ShipmentFile::class);
    }

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function podDocument()
    {
        return $this->belongsTo(Document::class, 'pod_document_id');
    }
}
