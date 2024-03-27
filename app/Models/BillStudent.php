<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class BillStudent extends Base
{
    use SoftDeletes;
    protected $table = 'bill_student';
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public const NOTPAY = 1;
    public const PAYED = 2;
    protected $appends = ['status_description'];
    public const STATUS = [
        self::NOTPAY => '未支付',
        self::PAYED => '已支付',
    ];

    public function getStatusDescriptionAttribute()
    {
        $status = $this->getAttribute('status');
        if (isset($status)) {
            return self::STATUS[$status];
        } else {
            return "";
        }
    }
}
