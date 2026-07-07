<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomsQuery extends Model
{
    protected $fillable = ['declaration_id', 'raised_at', 'query_text', 'response_text', 'responded_at', 'status'];

    protected function casts(): array
    {
        return ['raised_at' => 'datetime', 'responded_at' => 'datetime'];
    }

    public function declaration()
    {
        return $this->belongsTo(Declaration::class);
    }
}
