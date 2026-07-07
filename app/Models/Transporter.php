<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transporter extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'tin', 'phone', 'contact_person', 'rate_notes', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function trucks()
    {
        return $this->hasMany(Truck::class);
    }
}
