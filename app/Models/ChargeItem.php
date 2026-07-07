<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChargeItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'category', 'default_price', 'vat_rate', 'is_active'];

    protected function casts(): array
    {
        return ['default_price' => 'decimal:2', 'vat_rate' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function fileCharges()
    {
        return $this->hasMany(FileCharge::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
