<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_name',
        'student_or_lead_name',
        'email',
        'phone',
        'subject',
        'message',
        'payload',
        'ip_address',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
