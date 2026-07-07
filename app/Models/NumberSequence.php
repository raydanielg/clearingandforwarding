<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberSequence extends Model
{
    protected $fillable = ['key', 'prefix', 'year', 'last_number'];

    protected function casts(): array
    {
        return ['year' => 'integer', 'last_number' => 'integer'];
    }
}
