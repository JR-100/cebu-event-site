<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
        'date_time',
        'location',
        'category',
        'image',
        'is_featured',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date_time', '>=', now())->orderBy('date_time');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date_time', $date);
    }
}
