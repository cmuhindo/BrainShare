<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubtopicContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'generated_course_id',
        'subtopic_title',
        'content',
        'json_content'
    ]; 
}
