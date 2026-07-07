<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingLine extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'code', 'contact_person', 'phone', 'email', 'address', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function shipmentFiles()
    {
        return $this->hasMany(ShipmentFile::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }
}
