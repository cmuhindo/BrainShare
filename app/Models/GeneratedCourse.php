<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedCourse extends Model
{
    use HasFactory;

    protected $table = 'generated_courses';

    protected $fillable = ['user_id', 'subscription_id', 'course_title', 'class', 'course_description', 'course_content', 'json_content'];

    protected $casts = [
        'json_content' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
