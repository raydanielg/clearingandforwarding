<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = ['date', 'category', 'description', 'amount', 'receipt_path', 'recorded_by'];

    protected function casts(): array
    {
        return ['date' => 'date', 'amount' => 'decimal:2'];
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
