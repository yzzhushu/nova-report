<?php

namespace Jshxl\Report\Models;

use Illuminate\Support\Str;
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
        'users'   => 'array',
    ];

    /**
     * 报表字段对照
     *
     * @return Attribute
     * */
    protected function fields(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => !is_array($value) || count($value) === 0 ? '{}' : json_encode($value)
        );
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }
}
