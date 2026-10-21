<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_key',
        'name',
        'subject',
        'body_html',
        'placeholders',
        'is_active',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'is_active' => 'boolean',
    ];

    public function render(array $data): array
    {
        $subject = $this->subject;
        $body = $this->body_html;

        foreach ($data as $key => $value) {
            $token = '{' . $key . '}';
            $subject = str_replace($token, (string) $value, $subject);
            $body = str_replace($token, (string) $value, $body);
        }

        return [
            'subject' => $subject,
            'body_html' => $body,
        ];
    }
}
