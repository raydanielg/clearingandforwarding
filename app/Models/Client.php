<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'tin', 'vrn', 'type', 'phone', 'email', 'address', 'credit_limit', 'payment_terms_days', 'is_active', 'created_by'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'credit_limit' => 'decimal:2'];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function shipmentFiles()
    {
        return $this->hasMany(ShipmentFile::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function insurancePolicies()
    {
        return $this->hasMany(InsurancePolicy::class);
    }

    public function insuranceClaims()
    {
        return $this->hasMany(InsuranceClaim::class);
    }
}
