<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'file_no', 'client_id', 'type', 'mode', 'status', 'bl_awb_no', 'vessel_flight',
        'shipping_line_id', 'port_of_loading', 'port_of_discharge', 'terminal_id',
        'eta', 'discharge_date', 'free_days_expiry', 'currency', 'exchange_rate',
        'assigned_declaration_officer_id', 'assigned_clearing_officer_id',
        'opened_by', 'opened_at', 'closed_at', 'remarks'
    ];

    protected function casts(): array
    {
        return [
            'eta' => 'date',
            'discharge_date' => 'date',
            'free_days_expiry' => 'date',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'exchange_rate' => 'decimal:4',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function shippingLine()
    {
        return $this->belongsTo(ShippingLine::class);
    }

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }

    public function declarationOfficer()
    {
        return $this->belongsTo(User::class, 'assigned_declaration_officer_id');
    }

    public function clearingOfficer()
    {
        return $this->belongsTo(User::class, 'assigned_clearing_officer_id');
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function consignments()
    {
        return $this->hasMany(Consignment::class);
    }

    public function declaration()
    {
        return $this->hasOne(Declaration::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class)->orderBy('happened_at');
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function charges()
    {
        return $this->hasMany(FileCharge::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function disbursements()
    {
        return $this->hasMany(Disbursement::class);
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
