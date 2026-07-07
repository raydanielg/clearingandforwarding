<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'is_mandatory_import', 'is_mandatory_export'];

    protected function casts(): array
    {
        return ['is_mandatory_import' => 'boolean', 'is_mandatory_export' => 'boolean'];
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
