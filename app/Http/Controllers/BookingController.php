<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\StudioClosure;
use Carbon\Carbon;

class BookingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FORM BOOKING
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $times = [];

        for ($i = 9; $i <= 22; $i++) {

            $times[] =
                sprintf('%02d.00', $i);

        }

        return view('booking', compact('times'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE BOOKING
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'nama' => 'required',
            'telepon' => 'required',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required'

        ]);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI TANGGAL
        |--------------------------------------------------------------------------
        */

        if ($request->tanggal < Carbon::today()->format('Y-m-d')) {

            return back()->with(
                'error',
                'Tidak bisa booking hari sebelumnya'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI JAM
        |--------------------------------------------------------------------------
        */

        if ($request->jam_mulai >= $request->jam_selesai) {

            return back()->with(
                'error',
                'Jam selesai harus lebih besar'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI HARI TUTUP STUDIO
        |--------------------------------------------------------------------------
        */

        if (Schema::hasTable('studio_closures') && StudioClosure::where('tanggal', $request->tanggal)->exists()) {

            return back()->with(
                'error',
                'Tidak bisa booking karena studio tutup pada tanggal tersebut'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | CEK TABRAKAN JADWAL
        |--------------------------------------------------------------------------
        */

        $schedules =
            Schedule::where(
                'tanggal',
                $request->tanggal
            )->get();

        foreach ($schedules as $schedule) {

            if (

                $request->jam_mulai < $schedule->jam_selesai
                &&
                $request->jam_selesai > $schedule->jam_mulai

            ) {

                return back()->with(
                    'error',
                    'Jadwal bertabrakan'
                );

            }

        }

        /*
        |--------------------------------------------------------------------------
        | CEK BOOKING PENDING
        |--------------------------------------------------------------------------
        */

        $bookings =
            Booking::where(
                'tanggal',
                $request->tanggal
            )
            ->where('status', 'pending')
            ->get();

        foreach ($bookings as $booking) {

            if (

                $request->jam_mulai < $booking->jam_selesai
                &&
                $request->jam_selesai > $booking->jam_mulai

            ) {

                return back()->with(
                    'error',
                    'Jadwal sudah dibooking'
                );

            }

        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN
        |--------------------------------------------------------------------------
        */

        Booking::create([

            'nama' => $request->nama,
            'telepon' => $request->telepon,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'status' => 'pending'

        ]);

        return redirect('/')
            ->with(
                'success',
                'Booking berhasil dikirim'
            );
    }
}