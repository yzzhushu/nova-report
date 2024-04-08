<?php

namespace Jshxl\Report\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class JshxlReport extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jshxl_report';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status'  => 'integer',
        'sort_no' => 'integer',
    ];

    /**
     * 设置报表字段存储值
     *
     * */
    protected function fields(): Attribute
    {
        return Attribute::make(
            get: fn($value) => is_null($value) ? [] : json_decode($value, true),
            set: fn($value) => is_null($value) ? '[]' : json_encode($value),
        );
    }
}
