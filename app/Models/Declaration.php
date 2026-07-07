<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Declaration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shipment_file_id', 'tansad_no', 'regime_code', 'declared_at', 'assessed_at',
        'duty_amount', 'vat_amount', 'other_taxes', 'total_taxes', 'status', 'officer_id', 'notes'
    ];

    protected function casts(): array
    {
        return [
            'declared_at' => 'datetime',
            'assessed_at' => 'datetime',
            'duty_amount' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'other_taxes' => 'decimal:2',
            'total_taxes' => 'decimal:2',
        ];
    }

    public function shipmentFile()
    {
        return $this->belongsTo(ShipmentFile::class);
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function items()
    {
        return $this->hasMany(DeclarationItem::class);
    }

    public function queries()
    {
        return $this->hasMany(CustomsQuery::class);
    }
}
