<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_no', 'shipment_file_id', 'client_id', 'date', 'due_date', 'currency',
        'exchange_rate', 'subtotal', 'vat_amount', 'total', 'amount_paid', 'status', 'kind', 'created_by'
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'due_date' => 'date',
            'exchange_rate' => 'decimal:4',
            'subtotal' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function shipmentFile()
    {
        return $this->belongsTo(ShipmentFile::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
