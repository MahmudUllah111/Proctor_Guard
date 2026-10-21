<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'session_token',
        'student_id_code',
        'student_name',
        'student_email',
        'ip_address',
        'user_agent',
        'status',
        'score',
        'max_score',
        'percentage',
        'passed',
        'strikes_count',
        'max_strikes',
        'started_at',
        'ends_at',
        'submitted_at',
        'termination_reason',
        'last_heartbeat_at',
        'question_order',
        'answers',
        'email_report_dispatched',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'passed' => 'boolean',
        'strikes_count' => 'integer',
        'max_strikes' => 'integer',
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'submitted_at' => 'datetime',
        'last_heartbeat_at' => 'datetime',
        'question_order' => 'array',
        'answers' => 'array',
        'email_report_dispatched' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            if (empty($session->session_token)) {
                $session->session_token = Str::random(40);
            }
        });
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function infractions(): HasMany
    {
        return $this->hasMany(ProctoringInfraction::class)->orderBy('id', 'desc');
    }

    public function remainingSeconds(): int
    {
        if (!$this->ends_at) {
            return 0;
        }

        $diff = Carbon::now()->diffInSeconds($this->ends_at, false);
        return max(0, $diff);
    }

    public function isExpired(): bool
    {
        if (!$this->ends_at) {
            return false;
        }

        return Carbon::now()->greaterThan($this->ends_at);
    }

    public function isTerminated(): bool
    {
        return $this->status === 'terminated_cheat';
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, ['completed', 'terminated_cheat']);
    }
}
