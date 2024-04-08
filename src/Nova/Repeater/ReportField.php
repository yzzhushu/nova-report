<?php

namespace Jshxl\Report\Nova\Repeater;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Http\Requests\NovaRequest;

class ReportField extends Repeatable
{
    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label(): string
    {
        return __('Fields Mapping');
    }

    /**
     * Get the fields displayed by the repeatable.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            Text::make(__('Field Code'), 'code')
                ->rules('required', 'max:64')
                ->placeholder('例如：barcode'),
            Text::make(__('Field Name'), 'name')
                ->rules('required', 'max:64')
                ->placeholder('例如：商品条码'),
            Boolean::make(__('Sorted Field'), 'sort')
                ->help('勾选后，该字段将支持排序功能（无需在SQL中实现）')
                ->default(false),
        ];
    }
}
