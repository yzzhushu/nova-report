<?php

use Illuminate\Support\Facades\Route;

Route::get('/{reportId}', 'ReportController@inertia')->name('jshxl.report');
