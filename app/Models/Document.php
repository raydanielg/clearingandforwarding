<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shipment_file_id', 'document_type_id', 'reference_no', 'file_path',
        'original_received', 'received_at', 'uploaded_by', 'verified_by', 'verified_at'
    ];

    protected function casts(): array
    {
        return [
            'original_received' => 'boolean',
            'received_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function shipmentFile()
    {
        return $this->belongsTo(ShipmentFile::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
