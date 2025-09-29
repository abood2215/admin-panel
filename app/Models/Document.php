<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'user_id','title','language','file_name','file_path','file_size','status',
        'extracted_text','content',
        'stream_id','year_id','specialty_id','subject_id',
        'semester', // <— جديد
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /* علاقات */
    public function user()      { return $this->belongsTo(User::class); }
    public function questions() { return $this->hasMany(DocumentQuestion::class)->orderBy('sort')->orderBy('id'); }

    public function stream()    { return $this->belongsTo(Stream::class); }
    public function year()      { return $this->belongsTo(Year::class); }

    public function specialty() { return $this->belongsTo(Specialty::class); }
    public function subject()   { return $this->belongsTo(Subject::class); }

    // إن كان عندك pivot للتصنيفات اتركه كما هو:
    public function categories(){ return $this->belongsToMany(\App\Models\Category::class); }
}
