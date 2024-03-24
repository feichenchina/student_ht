<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends User
{
    use SoftDeletes;

    protected $table = 'teacher';
}
