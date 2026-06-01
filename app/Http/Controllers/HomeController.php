<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\StudioClosure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | START WEEK
        |--------------------------------------------------------------------------
        */

        $start = $request->start
            ? Carbon::parse($request->start)
            : Carbon::now()->startOfWeek();

        /*
        |--------------------------------------------------------------------------
        | ARRAY HARI
        |--------------------------------------------------------------------------
        */

        $days = [];

        for ($i = 0; $i < 7; $i++) {

            $days[] =
                $start->copy()->addDays($i);

        }

        /*
        |--------------------------------------------------------------------------
        | ARRAY JAM
        |--------------------------------------------------------------------------
        */

        $times = [];

        for ($i = 9; $i <= 22; $i++) {

            $times[] =
                sprintf('%02d.00', $i)
                . ' - ' .
                sprintf('%02d.00', $i + 1);

        }

        /*
        |--------------------------------------------------------------------------
        | JADWAL
        |--------------------------------------------------------------------------
        */

        $schedules = Schedule::all();

        if (Schema::hasTable('studio_closures')) {
            $closures = StudioClosure::all();
            $closedDates = $closures
                ->pluck('tanggal')
                ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                ->toArray();
        } else {
            $closures = collect();
            $closedDates = [];
        }

        /*
        |--------------------------------------------------------------------------
        | NAVIGASI MINGGU
        |--------------------------------------------------------------------------
        */

        $prevWeek =
            $start->copy()
            ->subWeek()
            ->format('Y-m-d');

        $nextWeek =
            $start->copy()
            ->addWeek()
            ->format('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('home', compact(
            'days',
            'times',
            'schedules',
            'closures',
            'closedDates',
            'prevWeek',
            'nextWeek'
        ));
    }

    public function calendarOnly(Request $request)
    {
        $start = $request->start
            ? Carbon::parse($request->start)
            : Carbon::now()->startOfWeek();

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $start->copy()->addDays($i);
        }

        $times = [];
        for ($i = 9; $i <= 22; $i++) {
            $times[] = sprintf('%02d.00', $i) . ' - ' . sprintf('%02d.00', $i + 1);
        }

        $schedules = Schedule::all();
        if (Schema::hasTable('studio_closures')) {
            $closures = StudioClosure::all();
            $closedDates = $closures
                ->pluck('tanggal')
                ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                ->toArray();
        } else {
            $closures = collect();
            $closedDates = [];
        }

        $prevWeek = $start->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $start->copy()->addWeek()->format('Y-m-d');

        return view('calendar-only', compact(
            'days',
            'times',
            'schedules',
            'closures',
            'closedDates',
            'prevWeek',
            'nextWeek'
        ));
    }

    public function exportSchedule()
    {
        $columns = ['Nama', 'Telepon', 'Tanggal', 'Jam Mulai', 'Jam Selesai'];

        $callback = function () use ($columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            Schedule::orderBy('tanggal')
                ->orderBy('jam_mulai')
                ->chunk(100, function ($schedules) use ($handle) {
                    foreach ($schedules as $schedule) {
                        fputcsv($handle, [
                            $schedule->nama,
                            $schedule->telepon,
                            $schedule->tanggal,
                            $schedule->jam_mulai,
                            $schedule->jam_selesai,
                        ]);
                    }
                });

            fclose($handle);
        };

        return response()->streamDownload($callback, 'jadwal-studio.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="jadwal-studio.csv"',
        ]);
    }
}