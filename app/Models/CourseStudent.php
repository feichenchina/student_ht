<?php

namespace App\Models;

class CourseStudent extends Base
{
    protected $table = 'course_student';

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
