<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Insurer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code', 'contact_person', 'phone', 'email', 'address', 'license_no', 'is_active'
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function policies()
    {
        return $this->hasMany(InsurancePolicy::class);
    }
}
