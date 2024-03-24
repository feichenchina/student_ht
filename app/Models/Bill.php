<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Base
{
    use SoftDeletes;
    protected $table = 'bill';
    public const NOTSEND = 1;
    public const SENDED = 2;
    public const SUCCESS = 3;
    protected $appends = ['status_description'];
    public const STATUS = [
        self::NOTSEND => '未发送',
        self::SENDED => '已发送',
        self::SUCCESS => '已成功',
    ];

    public function getStatusDescriptionAttribute()
    {
        $status = $this->getAttribute('status');
        return self::STATUS[$status];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsToMany(Student::class)->withPivot('student_id');
    }
}
