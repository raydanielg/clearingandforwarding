<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HsCode extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'description', 'duty_rate', 'vat_rate', 'notes'];

    protected function casts(): array
    {
        return ['duty_rate' => 'decimal:2', 'vat_rate' => 'decimal:2'];
    }

    public function declarationItems()
    {
        return $this->hasMany(DeclarationItem::class);
    }
}
