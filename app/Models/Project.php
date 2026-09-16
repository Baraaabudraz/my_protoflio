<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title', 'description', 'icon', 'image', 'stack',
        'github_url', 'live_url', 'featured', 'sort_order', 'visible'
    ];

    protected $casts = [
        'stack' => 'array',
        'featured' => 'boolean',
        'visible' => 'boolean',
    ];

    public function scopeVisible($query) {
        return $query->where('visible', true)->orderBy('featured', 'desc')->orderBy('sort_order');
    }
}
