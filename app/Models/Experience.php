<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $fillable = [
        'title', 'company', 'date_range', 'description', 'tags', 'sort_order', 'visible'
    ];

    protected $casts = [
        'tags' => 'array',
        'visible' => 'boolean',
    ];

    public function scopeVisible($query) {
        return $query->where('visible', true)->orderBy('sort_order');
    }
}
