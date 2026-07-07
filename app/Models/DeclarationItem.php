<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeclarationItem extends Model
{
    protected $fillable = ['declaration_id', 'hs_code_id', 'description', 'qty', 'customs_value', 'duty_rate', 'computed_duty'];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:2',
            'customs_value' => 'decimal:2',
            'duty_rate' => 'decimal:2',
            'computed_duty' => 'decimal:2',
        ];
    }

    public function declaration()
    {
        return $this->belongsTo(Declaration::class);
    }

    public function hsCode()
    {
        return $this->belongsTo(HsCode::class);
    }
}
