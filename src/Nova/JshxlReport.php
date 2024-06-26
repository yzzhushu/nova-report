<?php

namespace Jshxl\Report\Nova;

use Jshxl\ListBox\ListBox;
use App\Nova\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
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
            Text::make(__('Report UUID'), 'uuid')
                ->exceptOnForms()
                ->displayUsing(function ($uuid) use ($request) {
                    return $request->isResourceDetailRequest() ? $uuid : substr($uuid, 0, 8) . '...';
                })
                ->textAlign('center'),
            Text::make(__('Report Group'), 'group_name')
                ->rules('nullable', 'string', 'max:16')
                ->default(__('Default Group'))
                ->textAlign('center')
                ->suggestions($request->isFormRequest() ? DB::table('jshxl_report')
                    ->select('group_name')->distinct()->pluck('group_name')->toArray() : [])
                ->help(__('Report Group Name, such as: Sales, Inventory, etc.')),
            Text::make(__('Report Name'), 'report_name')
                ->textAlign('center')
                ->help(__('Report Name, such as: Sales Report, Inventory Report, etc.'))
                ->rules('required', 'max:24'),
            Text::make(__('Sort Number'), 'display_sort')
                ->default(10000)
                ->textAlign('center')
                ->withMeta(['type' => 'number', 'step' => 1, 'min' => 1, 'max' => 99999])
                ->help(__('Report Display Sort Number, default 10000, the larger the number, the higher the display position.'))
                ->rules('required', 'integer', 'min:1', 'max:99999'),

            Select::make(__('DataEase Type'), 'de_type')
                ->displayUsingLabels()
                ->options([
                    'dashboard' => '仪表板',
                    'dataV'     => '数据大屏',
                ])
                ->textAlign('center')
                ->help(__('The type of DataEase report.'))
                ->rules('required', 'string', 'in:dashboard,dataV')
                ->default('dashboard'),
            Stack::make(__('DataEase'), [
                Text::make(__('DataEase ID'), 'de_id')->textAlign('center'),
                Text::make(__('DataEase Chart'), 'chart_id')->textAlign('center'),
            ])->onlyOnIndex()->textAlign('center'),
            Text::make(__('DataEase ID'), 'de_id')
                ->hideFromIndex()
                ->rules('required', 'string', 'regex:/^[0-9]{1,128}$/')
                ->help(__('DataEase DV or DBS element id.')),
            Text::make(__('DataEase Chart'), 'chart_id')
                ->hideFromIndex()
                ->rules('nullable', 'string', 'regex:/^[0-9]{1,128}$/')
                ->help(__('DataEase chart element id.')),

            ListBox::make(__('Auth Users'), 'auth_users')
                ->options(config('jshxl_report.api_user_list'))
                ->method(config('jshxl_report.api_user_list_method'))
                ->displayTable()
                ->displayUsing(function ($users) {
                    return count($users) . ' ' . __('Report Users');
                })
                ->textAlign('center')
                ->help(__('Who can view this report? no one can view by default.'))
                ->formatInt(),
            Boolean::make(__('Report Status'), 'status')
                ->help(__('Report Status: 0, Disable; 1, Enable'))
                ->default(0),
            DateTime::make(__('Created At'), 'created_at')
                ->displayUsing(function ($created_at) {
                    return $created_at->format('Y-m-d H:i:s');
                })
                ->onlyOnDetail()
                ->textAlign('center'),
            DateTime::make(__('Updated At'), 'updated_at')
                ->displayUsing(function ($updated_at) {
                    return $updated_at->format('Y-m-d H:i:s');
                })
                ->exceptOnForms()
                ->textAlign('center'),
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
        return $query->orderByDesc('display_sort')->orderBy('id');
    }
}
