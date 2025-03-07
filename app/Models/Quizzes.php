<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quizzes extends Model
{
    use HasFactory;

    protected $fillable = [
        'generated_course_id',
        'topic_title',
        'quiz_content'
    ]; 
}
