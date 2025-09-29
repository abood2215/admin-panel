<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentQuestion extends Model
{
    protected $fillable = [
        'document_id',
        'question',
        'options',
        'correct_answer',
        'sort',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    // ============== Relationships ==============
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    // ============== Helpers ==============
    /** نمط إزالة البادئة A) أو الحروف العربية المقابلة */
    protected function stripLabelPrefix(string $s): string
    {
        // يزيل: A)  B)  C) ...  أو (أ/ب/ج/د/ه/و) مع أي مسافات
        return trim(preg_replace('/^\s*([A-F]|[أابجدهو])\)\s*/u', '', $s ?? ''));
    }

    /** تنظيف مصفوفة الخيارات إلى نصوص صافية */
    protected function normalizeOptions(?array $options): array
    {
        $out = [];
        foreach ((array) $options as $opt) {
            $opt = is_string($opt) ? $this->stripLabelPrefix($opt) : $opt;
            if (is_string($opt)) {
                $opt = trim($opt);
                if ($opt !== '') {
                    $out[] = $opt;
                }
            }
        }
        // نسمح حتى 6 خيارات كحد أقصى
        return array_values(array_slice($out, 0, 6));
    }

    // ============== Mutators ==============
    public function setOptionsAttribute($value): void
    {
        $this->attributes['options'] = json_encode($this->normalizeOptions(is_array($value) ? $value : []), JSON_UNESCAPED_UNICODE);
    }

    public function setCorrectAnswerAttribute($value): void
    {
        $value = is_string($value) ? $this->stripLabelPrefix($value) : $value;
        // خزن نصًا صافيًا؛ الـ Views هي اللي بتضيف البادئة شكليًا فقط
        $this->attributes['correct_answer'] = $value ? trim($value) : null;
    }

    // ============== Accessors ==============

    /** خيارات بدون بادئات (لو فيه بيانات قديمة فيها A) …) */
    public function getOptionsPlainAttribute(): array
    {
        return $this->normalizeOptions($this->options ?? []);
    }

    /**
     * خيارات مع بادج الحروف للعرض فقط:
     * كل عنصر: ['label' => 'A', 'text' => 'النصّ']
     */
    public function getLabeledOptionsAttribute(): array
    {
        $labels = ['A','B','C','D','E','F'];
        $plain  = $this->options_plain;
        $out    = [];
        foreach ($plain as $i => $text) {
            $out[] = [
                'label' => $labels[$i] ?? chr(65 + $i),
                'text'  => $text,
            ];
        }
        return $out;
    }

    /** correct_answer كنصّ صافي دائمًا (بدون A) ) */
    public function getCorrectAnswerPlainAttribute(): ?string
    {
        return $this->correct_answer ? $this->stripLabelPrefix($this->correct_answer) : null;
    }
}
