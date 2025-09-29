<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Year extends Model
{
    public $timestamps = false;
    protected $fillable = ['year'];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
