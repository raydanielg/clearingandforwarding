<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disbursement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shipment_file_id', 'agency_id', 'payee', 'description', 'amount', 'currency',
        'paid_at', 'method', 'reference_no', 'receipt_scan_document_id', 'approved_by', 'recorded_by'
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'paid_at' => 'datetime'];
    }

    public function shipmentFile()
    {
        return $this->belongsTo(ShipmentFile::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
