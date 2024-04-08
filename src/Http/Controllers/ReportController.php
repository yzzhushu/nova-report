<?php

namespace Jshxl\Report\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class ReportController
{
    /**
     * Report tool index page.
     * @param Request $request
     * @param string $reportId
     *
     * @return Response|ResponseFactory
     */
    public function inertia(Request $request, string $reportId): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia('Report', ['reportId' => $reportId]);
    }
}
