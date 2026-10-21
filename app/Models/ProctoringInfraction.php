<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProctoringInfraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'infraction_type',
        'severity',
        'strike_number',
        'details',
        'client_timestamp',
        'server_timestamp',
        'ip_address',
    ];

    protected $casts = [
        'strike_number' => 'integer',
        'server_timestamp' => 'datetime',
    ];

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function getFormattedTypeAttribute(): string
    {
        return match ($this->infraction_type) {
            'visibility_hidden' => 'Tab Switched / Hidden',
            'window_blur' => 'Window Focus Lost',
            'fullscreen_exit' => 'Fullscreen Exited',
            'devtools_opened' => 'DevTools Detected',
            'devtools_docked' => 'DevTools Docked',
            'inspect_shortcut_pressed' => 'Inspect Shortcut Triggered',
            'context_menu_attempt' => 'Right-Click Context Menu',
            'tamper_attempt' => 'DOM/Timer Tampering',
            default => ucwords(str_replace('_', ' ', $this->infraction_type)),
        };
    }
}
