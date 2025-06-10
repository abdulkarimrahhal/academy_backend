<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class student_courses extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'student_id',
        'course_id'
    ];

    // طريقة لجلب جميع الكورسات المسجل بها حسب ID الطالب
    public function getCoursesForStudent($studentId)
    {
        return $this->where('student_id', $studentId)->get();
    }
}
