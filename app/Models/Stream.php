<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stream extends Model
{
    protected $fillable = ['slug', 'name_ar', 'name_en'];

    public function specialties(): HasMany
    {
        return $this->hasMany(Specialty::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
