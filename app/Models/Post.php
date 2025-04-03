<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'featured_image',
        'published',
        'published_at'
    ];

    protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
