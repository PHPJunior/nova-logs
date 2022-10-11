<?php

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Requests\NovaRequest;

/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
| Here is where you may register Inertia routes for your tool. These are
| loaded by the ServiceProvider of the tool. The routes are protected
| by your tool's "Authorize" middleware by default. Now - go build!
|
*/

Route::get('/dashboard', function (NovaRequest $request) {
    return inertia('NovaLogs', [
        'baseUrl' => \Laravel\Nova\Nova::url('/nova-logs')
    ]);
});

Route::get('/list', function (NovaRequest $request) {
    return inertia('LogsTool', [
        'baseUrl' => \Laravel\Nova\Nova::url('/nova-logs')
    ]);
});

Route::get('/list/{date}/{level}', function (NovaRequest $request, $date, $level) {
    return inertia('ShowLogs', [
        'baseUrl' => \Laravel\Nova\Nova::url('/nova-logs'),
        'date' => $date,
        'level' => $level,
    ]);
});
