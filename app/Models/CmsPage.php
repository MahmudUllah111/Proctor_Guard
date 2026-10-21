<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content_widgets',
        'status',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'og_image',
        'is_home',
    ];

    protected $casts = [
        'content_widgets' => 'array',
        'is_home' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function getEffectiveSeoTitleAttribute(): string
    {
        return $this->seo_title ?: $this->title . ' - ' . config('app.name', 'Exam CMS');
    }
}
