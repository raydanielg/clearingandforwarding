<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceClaim extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'claim_no', 'insurance_policy_id', 'client_id', 'shipment_file_id', 'status',
        'incident_date', 'incident_description', 'loss_location', 'claimed_amount',
        'approved_amount', 'settled_amount', 'submitted_at', 'settled_at',
        'insurer_response', 'notes', 'handled_by'
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'submitted_at' => 'date',
            'settled_at' => 'date',
            'claimed_amount' => 'decimal:2',
            'approved_amount' => 'decimal:2',
            'settled_amount' => 'decimal:2',
        ];
    }

    public function policy()
    {
        return $this->belongsTo(InsurancePolicy::class, 'insurance_policy_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function shipmentFile()
    {
        return $this->belongsTo(ShipmentFile::class);
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
