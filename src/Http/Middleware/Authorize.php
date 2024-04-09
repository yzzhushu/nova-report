<?php

namespace Jshxl\Report\Http\Middleware;

use Laravel\Nova\Nova;
use Jshxl\Report\Report;
use Laravel\Nova\Tool;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request):mixed  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        $tool = collect(Nova::registeredTools())->first([$this, 'matchesTool']);

        return optional($tool)->authorize($request) ? $next($request) : abort(403);
    }

    /**
     * Determine whether this tool belongs to the package.
     *
     * @param  Tool  $tool
     * @return bool
     */
    public function matchesTool(Tool $tool): bool
    {
        return $tool instanceof Report;
    }
}
