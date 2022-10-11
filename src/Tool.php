<?php

namespace PhpJunior\NovaLogViewer;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool as BaseTool;

class Tool extends BaseTool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('nova-logs', __DIR__ . '/../dist/js/tool.js');
        Nova::style('nova-logs', __DIR__ . '/../dist/css/tool.css');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        return MenuSection::make('Log Viewer', [
            MenuItem::link('Dashboard', '/nova-logs/dashboard'),
            MenuItem::link('Logs', '/nova-logs/list'),
        ])
            ->collapsable()
            ->icon('document-text');
    }
}
