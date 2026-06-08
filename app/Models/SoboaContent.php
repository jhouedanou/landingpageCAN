<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoboaContent extends Model
{
    protected $fillable = [
        'title',
        'body',
        'image_path',
        'video_url',
        'cta_label',
        'cta_url',
        'type',
        'is_published',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public static array $types = [
        'annonce'    => 'Annonce',
        'evenement'  => 'Événement',
        'activation' => 'Activation terrain',
        'promo'      => 'Promotion',
        'galerie'    => 'Galerie',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('sort_order')->orderByDesc('published_at');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) return null;
        if (str_starts_with($this->image_path, 'http')) return $this->image_path;
        return asset('storage/' . $this->image_path);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::$types[$this->type] ?? $this->type;
    }
}
