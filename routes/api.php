<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::group(['namespace' => 'PhpJunior\NovaLogViewer\Http\Controllers'], function () {
    Route::get('get_chart_data','NovaLogViewerController@getChartData');
    Route::get('get_list_logs','NovaLogViewerController@getListLogs');
    Route::get('download/{date}','NovaLogViewerController@download');
    Route::get('get/{date}/{level}','NovaLogViewerController@showByLevel');

    Route::delete('delete','NovaLogViewerController@delete');
});
