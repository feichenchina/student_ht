<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends User
{
    use SoftDeletes;

    protected $table = 'student';

    public function bills()
    {
        return $this->belongsToMany(Bill::class)->withPivot('bill_id');
    }
}
