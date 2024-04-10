<?php

namespace Jshxl\Report;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Illuminate\Http\Request;

class Report extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot(): void
    {
        Nova::script('report', __DIR__ . '/../dist/js/tool.js');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param Request $request
     * @return null
     */
    public function menu(Request $request): mixed
    {
        if (!is_null(Nova::$mainMenuCallback)) return null;
        return ReportMenu::make($request);
    }
}
