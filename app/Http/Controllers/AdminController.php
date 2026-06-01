<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\StudioClosure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ADMIN
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | CEK LOGIN
        |--------------------------------------------------------------------------
        */

        if (!session('admin')) {

            return redirect('/login');

        }

        /*
        |--------------------------------------------------------------------------
        | BOOKING PENDING
        |--------------------------------------------------------------------------
        */

        $bookings =
            Booking::where('status', 'pending')
            ->get();

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

        [$scheduleSpreadsheetUrl, $scheduleSpreadsheetUpdatedAt] = $this->generateScheduleSpreadsheet();
        [$bookingSpreadsheetUrl, $bookingSpreadsheetUpdatedAt] = $this->generateBookingSpreadsheet();

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

        return view('dashboard-admin', compact(
            'bookings',
            'days',
            'times',
            'schedules',
            'closures',
            'closedDates',
            'prevWeek',
            'nextWeek',
            'scheduleSpreadsheetUrl',
            'scheduleSpreadsheetUpdatedAt',
            'bookingSpreadsheetUrl',
            'bookingSpreadsheetUpdatedAt'
        ));
    }

    private function generateScheduleSpreadsheet()
    {
        $directory = public_path('spreadsheets');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = $directory . '/jadwal-studio.csv';
        $handle = fopen($filePath, 'w');
        fputcsv($handle, ['Nama', 'Telepon', 'Tanggal', 'Jam Mulai', 'Jam Selesai']);

        Schedule::orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get()
            ->each(function ($schedule) use ($handle) {
                fputcsv($handle, [
                    $schedule->nama,
                    $schedule->telepon,
                    $schedule->tanggal,
                    $schedule->jam_mulai,
                    $schedule->jam_selesai,
                ]);
            });

        fclose($handle);

        return [
            asset('spreadsheets/jadwal-studio.csv'),
            date('d-m-Y H:i:s', filemtime($filePath)),
        ];
    }

    private function generateBookingSpreadsheet()
    {
        $directory = public_path('spreadsheets');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = $directory . '/booking-pending.csv';
        $handle = fopen($filePath, 'w');
        fputcsv($handle, ['Nama', 'Telepon', 'Tanggal', 'Jam Mulai', 'Jam Selesai']);

        Booking::where('status', 'pending')
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get()
            ->each(function ($booking) use ($handle) {
                fputcsv($handle, [
                    $booking->nama,
                    $booking->telepon,
                    $booking->tanggal,
                    $booking->jam_mulai,
                    $booking->jam_selesai,
                ]);
            });

        fclose($handle);

        return [
            asset('spreadsheets/booking-pending.csv'),
            date('d-m-Y H:i:s', filemtime($filePath)),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE BOOKING
    |--------------------------------------------------------------------------
    */

    public function approve($id)
    {
        /*
        |--------------------------------------------------------------------------
        | AMBIL BOOKING
        |--------------------------------------------------------------------------
        */

        $booking = Booking::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | SIMPAN KE SCHEDULE
        |--------------------------------------------------------------------------
        */

        if (Schema::hasTable('studio_closures') && StudioClosure::where('tanggal', $booking->tanggal)->exists()) {
            return redirect('/admin')->with('error', 'Tidak bisa approve booking karena studio tutup pada tanggal tersebut');
        }

        $scheduleData = [
            'nama' => $booking->nama,
            'tanggal' => $booking->tanggal,
            'jam_mulai' => $booking->jam_mulai,
            'jam_selesai' => $booking->jam_selesai
        ];

        if (Schema::hasColumn('schedules', 'telepon')) {
            $scheduleData['telepon'] = $booking->telepon;
        }

        Schedule::create($scheduleData);

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS
        |--------------------------------------------------------------------------
        */

        $booking->status = 'approved';

        $booking->save();

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect('/admin');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT BOOKING
    |--------------------------------------------------------------------------
    */

    public function reject($id)
    {
        /*
        |--------------------------------------------------------------------------
        | AMBIL BOOKING
        |--------------------------------------------------------------------------
        */

        $booking = Booking::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | UPDATE STATUS
        |--------------------------------------------------------------------------
        */

        $booking->status = 'rejected';

        $booking->save();

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect('/admin');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE MANUAL SCHEDULE
    |--------------------------------------------------------------------------
    */

    public function storeSchedule(Request $request)
    {
        if (!session('admin')) {
            return redirect('/login');
        }

        $request->validate([
            'nama' => 'required',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'telepon' => 'nullable'
        ]);

        if ($request->jam_mulai >= $request->jam_selesai) {
            return back()->with('error', 'Jam selesai harus lebih besar dari jam mulai');
        }

        if (Schema::hasTable('studio_closures') && StudioClosure::where('tanggal', $request->tanggal)->exists()) {
            return back()->with('error', 'Tidak bisa menambahkan jadwal karena studio tutup pada tanggal tersebut');
        }

        $existingSchedules = Schedule::where('tanggal', $request->tanggal)->get();

        foreach ($existingSchedules as $schedule) {
            if (
                $request->jam_mulai < $schedule->jam_selesai
                &&
                $request->jam_selesai > $schedule->jam_mulai
            ) {
                return back()->with('error', 'Jadwal bertabrakan dengan jadwal yang sudah ada');
            }
        }

        $data = [
            'nama' => $request->nama,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai
        ];

        if (Schema::hasColumn('schedules', 'telepon')) {
            $data['telepon'] = $request->telepon;
        }

        Schedule::create($data);

        return redirect('/admin')->with('success', 'Jadwal manual berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | CLOSE DAY
    |--------------------------------------------------------------------------
    */

    public function closeDay(Request $request)
    {
        if (!session('admin')) {
            return redirect('/login');
        }

        $request->validate([
            'tanggal_tutup' => 'required|date',
            'alasan' => 'nullable|string|max:255'
        ]);

        $tanggal = $request->tanggal_tutup;

        $existingSchedules = Schedule::where('tanggal', $tanggal)->count();

        if ($existingSchedules > 0) {
            return back()->with('error', 'Tidak bisa menutup hari yang sudah memiliki jadwal. Hapus jadwal terlebih dahulu.');
        }

        if (StudioClosure::where('tanggal', $tanggal)->exists()) {
            return back()->with('error', 'Hari tersebut sudah ditutup.');
        }

        StudioClosure::create([
            'tanggal' => $tanggal,
            'alasan' => $request->alasan
        ]);

        return redirect('/admin')->with('success', 'Operasional studio berhasil ditutup pada tanggal yang dipilih.');
    }

    public function openDay($id)
    {
        if (!session('admin')) {
            return redirect('/login');
        }

        $closure = StudioClosure::findOrFail($id);
        $closure->delete();

        return redirect('/admin')->with('success', 'Operasional studio berhasil dibuka kembali.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE SCHEDULE
    |--------------------------------------------------------------------------
    */

    public function deleteSchedule($id)
    {
        /*
        |--------------------------------------------------------------------------
        | CEK LOGIN
        |--------------------------------------------------------------------------
        */

        if (!session('admin')) {

            return redirect('/login');

        }

        /*
        |--------------------------------------------------------------------------
        | HAPUS JADWAL
        |--------------------------------------------------------------------------
        */

        $schedule = Schedule::findOrFail($id);

        $schedule->delete();

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect('/admin');
    }
}