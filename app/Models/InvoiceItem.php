<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'charge_item_id', 'description', 'qty', 'unit_price', 'vat_rate', 'line_total'];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'vat_rate' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function chargeItem()
    {
        return $this->belongsTo(ChargeItem::class);
    }
}
