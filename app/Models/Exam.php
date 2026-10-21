<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'course_code',
        'description',
        'instructions',
        'duration_minutes',
        'pass_percentage',
        'max_strikes',
        'access_passcode',
        'randomize_questions',
        'randomize_options',
        'is_published',
        'is_released',
        'released_at',
        'anti_cheat_strictness',
        'enforce_fullscreen',
        'block_clipboard',
        'block_devtools',
        'watermark_enabled',
        'watermark_custom_text',
        'scheduled_start',
        'scheduled_end',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'pass_percentage' => 'decimal:2',
        'max_strikes' => 'integer',
        'randomize_questions' => 'boolean',
        'randomize_options' => 'boolean',
        'is_published' => 'boolean',
        'is_released' => 'boolean',
        'enforce_fullscreen' => 'boolean',
        'block_clipboard' => 'boolean',
        'block_devtools' => 'boolean',
        'watermark_enabled' => 'boolean',
        'released_at' => 'datetime',
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($exam) {
            if (empty($exam->slug)) {
                $exam->slug = Str::slug($exam->title) . '-' . Str::random(5);
            }
        });
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    public function totalPoints(): float
    {
        return (float) $this->questions()->sum('points');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
