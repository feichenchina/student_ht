<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Base
{
    use SoftDeletes;
    protected $table = 'course';
    protected $appends = ['bill_id'];
    protected $hidden = ['updated_at', 'deleted_at', 'bill'];
    public function bill()
    {
        return $this->hasOne(Bill::class);
    }
    // Course 模型中定义一个名为 billIds 的访问器
    public function getBillIdAttribute()
    {
        if (!isset($this->bill)) {
            return "";
        } else {
            return (string) $this->bill["id"];
        }
    }
}
