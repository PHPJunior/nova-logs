<?php

namespace PhpJunior\NovaLogViewer\Http\Controllers;

use Arcanedev\LogViewer\Exceptions\LogNotFoundException;
use Arcanedev\LogViewer\Tables\StatsTable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Arcanedev\LogViewer\Contracts\LogViewer as LogViewerContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class NovaLogViewerController extends Controller
{
    /**
     * @var LogViewerContract
     */
    private $logViewer;

    /** @var int */
    protected $perPage = 20;

    /**
     * NovaLogViewerController constructor.
     * @param LogViewerContract $logViewer
     */
    public function __construct(LogViewerContract $logViewer)
    {
        $this->logViewer = $logViewer;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData()
    {
        $stats     = $this->logViewer->statsTable();
        $chartData = $this->prepareChartData($stats);
        $percents  = $this->calcPercentages($stats->footer(), $stats->header());

        return response()->json([
            'chartData' => $chartData,
            'percents' => $percents
        ], 200);
    }

    /**
     * List all logs.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListLogs(Request $request)
    {
        $stats   = $this->logViewer->statsTable();
        $headers = $stats->header();
        $rows    = $this->paginate($stats->rows(), $request);

        return response()->json([
            'headers' => $headers,
            'rows' => $rows
        ]);
    }

    /**
     * Filter the log entries by level.
     *
     * @param  string $date
     *
     * @param $level
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showByLevel($date, $level)
    {
        $log = $this->getLogOrFail($date);
        $levels  = $this->logViewer->levelsNames();
        $entries = $this->logViewer->entries($date, $level)->paginate($this->perPage);
        $menus = $log->menu();

        return response()->json([
            'log' => $log,
            'info' => [
                'path' => $log->getPath(),
                'size' => $log->size(),
                'entries' => $entries->total(),
                'created_at' => $log->createdAt()->format('Y-m-d H:i:s'),
                'updated_at' => $log->updatedAt()->format('Y-m-d H:i:s')
            ],
            'levels' => $levels,
            'level' => $level,
            'entries' => $entries,
            'menus' => $menus
        ], 200);
    }

    /**
     * Download the log
     *
     * @param  string  $date
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($date)
    {
        return $this->logViewer->download($date);
    }

    /**
     * Delete a log.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Arcanedev\LogViewer\Exceptions\FilesystemException
     */
    public function delete(Request $request)
    {
        $date = $request->get('date');
        return response()->json([
            'result' => $this->logViewer->delete($date) ? 'success' : 'error'
        ]);
    }

    /**
     * Prepare chart data.
     *
     * @param  \Arcanedev\LogViewer\Tables\StatsTable $stats
     *
     * @return array
     */
    protected function prepareChartData(StatsTable $stats)
    {
        $totals = $stats->totals()->all();

        return [
            'labels'   => Arr::pluck($totals, 'label'),
            'datasets' => [
                [
                    'data'                 => Arr::pluck($totals, 'value'),
                    'backgroundColor'      => Arr::pluck($totals, 'color'),
                    'hoverBackgroundColor' => Arr::pluck($totals, 'highlight'),
                ],
            ],
        ];
    }

    /**
     * Calculate the percentage.
     *
     * @param  array  $total
     * @param  array  $names
     *
     * @return array
     */
    protected function calcPercentages(array $total, array $names)
    {
        $percents = [];
        $all      = Arr::get($total, 'all');

        foreach ($total as $level => $count) {
            $percents[$level] = [
                'name'    => $names[$level],
                'count'   => $count,
                'percent' => $all ? round(($count / $all) * 100, 2) : 0,
                'backgroundColor' => config('log-viewer.colors.levels.'. $level)
            ];
        }

        return $percents;
    }

    /**
     * Paginate logs.
     *
     * @param  array                     $data
     * @param  \Illuminate\Http\Request  $request
     *
     * @return LengthAwarePaginator
     */
    protected function paginate(array $data, Request $request)
    {
        $data = new Collection($data);
        $page = $request->get('page', 1);
        $path = $request->url();

        return new LengthAwarePaginator(
            $data->forPage($page, $this->perPage),
            $data->count(),
            $this->perPage,
            $page,
            compact('path')
        );
    }

    /**
     * Get a log or fail
     *
     * @param  string  $date
     *
     * @return \Arcanedev\LogViewer\Entities\Log|null
     */
    protected function getLogOrFail($date)
    {
        $log = null;

        try {
            $log = $this->logViewer->get($date);
        }
        catch (LogNotFoundException $e) {
            abort(404, $e->getMessage());
        }

        return $log;
    }
}
