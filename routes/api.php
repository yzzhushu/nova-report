<?php

use Illuminate\Support\Facades\Route;

Route::post('auth/user/lists', 'ReportController@getUsers');
Route::post('structure', 'ReportController@getStructure');
