<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsurancePolicy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'policy_no', 'client_id', 'shipment_file_id', 'insurer_id', 'type', 'status',
        'cover_start_date', 'cover_end_date', 'origin', 'destination', 'cargo_description',
        'cargo_value', 'cargo_currency', 'sum_insured', 'premium_amount', 'premium_currency',
        'commission_rate', 'commission_amount', 'terms_conditions', 'issued_by', 'issued_at',
        'notes'
    ];

    protected function casts(): array
    {
        return [
            'cover_start_date' => 'date',
            'cover_end_date' => 'date',
            'cargo_value' => 'decimal:2',
            'sum_insured' => 'decimal:2',
            'premium_amount' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'issued_at' => 'datetime',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function shipmentFile()
    {
        return $this->belongsTo(ShipmentFile::class);
    }

    public function insurer()
    {
        return $this->belongsTo(Insurer::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function claims()
    {
        return $this->hasMany(InsuranceClaim::class);
    }
}
