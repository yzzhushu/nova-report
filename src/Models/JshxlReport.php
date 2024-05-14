<?php

namespace Jshxl\Report\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

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
        'status'       => 'integer',
        'display_sort' => 'integer',
        'auth_users'   => 'array',
    ];

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
