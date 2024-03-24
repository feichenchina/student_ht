<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * 基础model
 * {@inheritdoc}
 * @DateTime 2021-03-10
 * @method static \Illuminate\Database\Eloquent\Builder|$this newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|$this newQuery()
 * @method static \Illuminate\Database\Query\Builder|$this onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|$this query()
 * @method static \Illuminate\Database\Eloquent\Builder|$this where()
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhere()
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereIn()
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereNotIn()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereIn()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereNotIn()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereRaw()
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereRaw()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereNull()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereNotNull()
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereNull()
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereNotNull()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereBetween()
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereBetween()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereNotBetween()
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereNotBetween()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereDate()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereDay()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereYear()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereTime()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereColumn()
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereColumn()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereExists()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereJsonLength()
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereJsonContains()
 * @method static \Illuminate\Database\Eloquent\Builder|$this from()
 * @method static \Illuminate\Database\Eloquent\Builder|$this latest()
 * @method static \Illuminate\Database\Eloquent\Builder|$this oldest()
 * @method static \Illuminate\Database\Eloquent\Builder|$this limit()
 * @method static \Illuminate\Database\Eloquent\Builder|$this chunk()
 * @method static \Illuminate\Database\Eloquent\Builder|$this skip()
 * @method static \Illuminate\Database\Eloquent\Builder|$this take()
 * @method static \Illuminate\Database\Eloquent\Builder|$this when()
 * @method static \Illuminate\Database\Eloquent\Builder|$this select()
 * @method static \Illuminate\Database\Eloquent\Builder|$this addSelect()
 * @method static \Illuminate\Database\Eloquent\Builder|$this selectRaw()
 * @method static \Illuminate\Database\Eloquent\Builder|$this reorder()
 * @method static \Illuminate\Database\Eloquent\Builder|$this inRandomOrder()
 * @method static \Illuminate\Database\Eloquent\Builder|$this orderBy()
 * @method static \Illuminate\Database\Eloquent\Builder|$this groupBy()
 * @method static \Illuminate\Database\Eloquent\Builder|$this having()
 * @method static \Illuminate\Database\Eloquent\Builder|$this havingRaw()
 * @method static \Illuminate\Database\Eloquent\Builder|$this orHavingRaw()
 * @method static \Illuminate\Database\Eloquent\Builder|$this orderByRaw()
 * @method static \Illuminate\Database\Eloquent\Builder|$this groupByRaw()
 * @method static \Illuminate\Database\Eloquent\Builder|$this join()
 * @method static \Illuminate\Database\Eloquent\Builder|$this leftJoin()
 * @method static \Illuminate\Database\Eloquent\Builder|$this rightJoin()
 * @method static \Illuminate\Database\Eloquent\Builder|$this crossJoin()
 * @method static \Illuminate\Database\Eloquent\Builder|$this joinSub()
 * @method static \Illuminate\Database\Eloquent\Builder|$this union()
 * @method static \Illuminate\Database\Eloquent\Builder|$this distinct()
 * @method static \Illuminate\Database\Eloquent\Builder|$this sharedLock()
 * @method static \Illuminate\Database\Eloquent\Builder|$this lockForUpdate()
 * @method static \Illuminate\Database\Eloquent\Builder|$this with()
 * @method static \Illuminate\Database\Eloquent\Builder|$this withCount()
 * @method static \Illuminate\Database\Eloquent\Collection get()
 * @method static \Illuminate\Database\Eloquent\Collection pluck()
 * @method static \Illuminate\Pagination\Paginator|\Illuminate\Database\Eloquent\Collection simplePaginate()
 * @method static \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection paginate()
 * @method static mixed value()
 * @method static string dd() 显示调试信息并停止执行请求
 * @method static string dump() 显示调试信息
 * @method static int count()
 * @method static float avg()
 * @method static float max()
 * @method static float min()
 * @method static bool exists()
 * @method static \Illuminate\Database\Eloquent\Model|$this create()
 * @method static \Illuminate\Database\Eloquent\Model|$this|null find()
 * @method static \Illuminate\Database\Eloquent\Model|$this findOrFail()
 * @method static \Illuminate\Database\Eloquent\Model|$this|null first()
 * @method static \Illuminate\Database\Eloquent\Model|$this firstOrFail()
 * @method static \Illuminate\Database\Query\Builder|$this withTrashed()
 * @method static \Illuminate\Database\Query\Builder|$this withoutTrashed()
 * @mixin \Eloquent
 */
class Base extends BaseModel
{
    // 自定义主键
    protected $primaryKey = 'id';

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DELETED_AT = 'deleted_at';

    protected $guarded = [];
    protected $hidden = ['updated_at', 'deleted_at'];
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
