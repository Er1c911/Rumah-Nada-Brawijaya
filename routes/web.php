<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index']);
Route::get('/calendar', [HomeController::class, 'calendarOnly']);
Route::get('/export-schedule', [HomeController::class, 'exportSchedule']);

/*
|--------------------------------------------------------------------------
| BOOKING
|--------------------------------------------------------------------------
*/

Route::get('/booking', [BookingController::class, 'create']);

Route::post('/booking', [BookingController::class, 'store']);

/*
|--------------------------------------------------------------------------
| LOGIN ADMIN
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {

    return view('login');

});

Route::post('/login', function (Request $request) {

    $username = 'DivisiRT';

    $password = 'RTKeras';

    if (
        $request->username == $username
        &&
        $request->password == $password
    ) {

        session([
            'admin' => true
        ]);

        return redirect('/admin');

    }

    return back()->with(
        'error',
        'Username atau password salah'
    );

});

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::get('/logout', function () {

    session()->forget('admin');

    return redirect('/login');

});

/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/admin', [AdminController::class, 'index']);

/*
|--------------------------------------------------------------------------
| APPROVE BOOKING
|--------------------------------------------------------------------------
*/

Route::post('/approve/{id}', [AdminController::class, 'approve']);

/*
|--------------------------------------------------------------------------
| REJECT BOOKING
|--------------------------------------------------------------------------
*/

Route::post('/reject/{id}', [AdminController::class, 'reject']);

/*
|--------------------------------------------------------------------------
| MANUAL SCHEDULE
|--------------------------------------------------------------------------
*/

Route::post('/admin/schedule', [AdminController::class, 'storeSchedule']);

/*
|--------------------------------------------------------------------------
| CLOSE DAY
|--------------------------------------------------------------------------
*/

Route::post('/admin/close-day', [AdminController::class, 'closeDay']);
Route::post('/admin/open-day/{id}', [AdminController::class, 'openDay']);

/*
|--------------------------------------------------------------------------
| DELETE SCHEDULE
|--------------------------------------------------------------------------
*/

Route::post('/delete-schedule/{id}', [AdminController::class, 'deleteSchedule']);