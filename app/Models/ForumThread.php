<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumThread extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id', 'user_id', 'title', 'pinned', 'locked', 'views',
    ];

    protected $casts = [
        'pinned' => 'boolean',
        'locked' => 'boolean',
    ];

    public function course() { return $this->belongsTo(Course::class); }
    public function author() { return $this->belongsTo(User::class, 'user_id'); }
    public function posts()  { return $this->hasMany(ForumPost::class); }

    public function latestPost()
    {
        return $this->hasOne(ForumPost::class)->latestOfMany();
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
