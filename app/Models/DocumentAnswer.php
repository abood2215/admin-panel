<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_id',
        'question_index',
        'answer',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
