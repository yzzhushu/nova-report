<?php

namespace Jshxl\Report\Nova;

use Jshxl\ListBox\ListBox;
use App\Nova\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class JshxlReport extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\Jshxl\Report\Models\JshxlReport>
     */
    public static string $model = \Jshxl\Report\Models\JshxlReport::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['uuid', 'name', 'group_name'];

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label(): string
    {
        return __('Jshxl Report');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            Text::make(__('Report Group'), 'group_name')
                ->help('分组名称，用于报表归类展示，建议4个字，例如：销售报表')
                ->rules('nullable', 'string', 'max:16')
                ->suggestions($request->isFormRequest() ? DB::table('jshxl_report')
                    ->select('group_name')->distinct()->pluck('group_name')->toArray() : [])
                ->textAlign('center'),
            Text::make(__('Report UUID'), 'uuid')
                ->exceptOnForms()
                ->displayUsing(function ($uuid) use ($request) {
                    if ($request->isResourceDetailRequest()) return $uuid;
                    return substr($uuid, 0, 4) . '...' . substr($uuid, -4);
                })
                ->textAlign('center'),
            Text::make(__('Report Name'), 'name')
                ->help('报表名称，建议6个字以内，例如：残次品销售')
                ->rules('required', 'max:24')
                ->textAlign('center'),
            Text::make(__('Display Sorting'), 'sort_no')
                ->default(10000)
                ->hideFromIndex()
                ->withMeta(['type' => 'number', 'step' => 1, 'min' => 1, 'max' => 99999])
                ->help('报表展示顺序，数字越大越靠前，最小为1，最大为99999')
                ->rules('required', 'integer', 'min:1', 'max:99999'),

            $request->isResourceIndexRequest() ?
                Text::make(__('Report SQL'), 'sql')
                    ->textAlign('center')
                    ->displayUsing(function ($sql) {
                        return substr($sql, 0, 24) . '...';
                    }) :
                Textarea::make(__('Report SQL'), 'sql')
                    ->help('报表SQL语句，支持变量替换，详情见报表撰写手册')
                    ->rules('required', 'string', 'min:1')
                    ->rows(24)
                    ->alwaysShow(),

            KeyValue::make(__('Report Fields'), 'fields')
                ->keyLabel(__('Field Code'))
                ->valueLabel(__('Field Name')),
            ListBox::make(__('Report Users'), 'users')
                ->options(config('jshxl_report.api_user_list'))
                ->method(config('jshxl_report.api_user_list_method'))
                ->displayTable()
                ->formatInt()
                ->displayUsing(function ($users) {
                    return count($users) . ' 位用户';
                })
                ->textAlign('center'),
            Boolean::make(__('Report Status'), 'status')
                ->help('报表启用状态，默认停用，即：所有人均无法查看该报表')
                ->default(0),

            DateTime::make(__('Created At'), 'created_at')
                ->onlyOnDetail()
                ->displayUsing(function ($created_at) {
                    return $created_at->format('Y-m-d H:i:s');
                }),
            DateTime::make(__('Updated At'), 'updated_at')
                ->textAlign('center')
                ->displayUsing(function ($updated_at) {
                    return $updated_at->format('Y-m-d H:i:s');
                })
                ->exceptOnForms(),
        ];
    }

    /**
     * Build an "index" query for the given resource.
     * @param NovaRequest $request
     * @param Builder $query
     *
     * @return Builder
     */
    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        $query->getQuery()->orders = [];
        return $query->orderByDesc('sort_no')->orderBy('id');
    }
}
