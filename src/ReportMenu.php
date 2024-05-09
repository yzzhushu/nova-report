<?php

namespace Jshxl\Report;

use Illuminate\Http\Request;
use Jshxl\Report\Models\JshxlReport;
use Laravel\Nova\Exceptions\NovaException;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;

class ReportMenu
{
    /**
     * 生成报表菜单
     * @param Request $request
     *
     * @return array
     *
     * @throws NovaException
     */
    public static function make(Request $request): array
    {
        if (is_null($request->user()) || !isset($request->user()->id)) return [];

        $reports = JshxlReport::query()
            ->whereJsonContains('auth_users', $request->user()->id)
            ->where('status', 1)
            ->select('uuid', 'report_name', 'group_name')
            ->orderByDesc('display_sort')->orderBy('id')
            ->get();
        if ($reports->count() === 0) return [];

        $lists = [];
        $group = [];
        $_name = '';
        foreach ($reports as $report) {
            if ($report->group_name !== $_name) {
                if (count($group) > 0)
                    $lists[] = MenuGroup::make($_name, $group)->collapsable();
                $group = [];
                $_name = $report->group_name;
            }
            $group[] = MenuItem::link($report->report_name, 'jshxl-report/' . $report->uuid);
        }
        if (count($group) > 0) $lists[] = MenuGroup::make($_name, $group)->collapsable();

        return count($lists) > 0 ? [
            MenuSection::make(__('Jshxl Report'), $lists)
                ->icon('presentation-chart-bar')->collapsable(),
        ] : [];
    }
}
