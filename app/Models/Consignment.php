<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consignment extends Model
{
    protected $fillable = [
        'shipment_file_id', 'description', 'marks_numbers', 'packages_count',
        'package_type', 'gross_weight', 'volume_cbm', 'cargo_value', 'cargo_currency'
    ];

    protected function casts(): array
    {
        return [
            'gross_weight' => 'decimal:2',
            'volume_cbm' => 'decimal:3',
            'cargo_value' => 'decimal:2',
        ];
    }

    public function shipmentFile()
    {
        return $this->belongsTo(ShipmentFile::class);
    }

    public function containers()
    {
        return $this->hasMany(Container::class);
    }
}
