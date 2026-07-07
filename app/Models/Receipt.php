<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use SoftDeletes;

    protected $fillable = ['receipt_no', 'invoice_id', 'client_id', 'date', 'amount', 'method', 'reference', 'received_by'];

    protected function casts(): array
    {
        return ['date' => 'date', 'amount' => 'decimal:2'];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
