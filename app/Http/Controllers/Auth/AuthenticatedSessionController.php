<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login admin
     */
    public function store(Request $request)
    {
        $username = $request->email;
        $password = $request->password;

        /*
        |--------------------------------------------------------------------------
        | Login Admin Tetap
        |--------------------------------------------------------------------------
        */

        if ($username === 'admin' && $password === '123') {

            session([
                'admin_logged_in' => true
            ]);

            return redirect('/');
        }

        /*
        |--------------------------------------------------------------------------
        | Jika Login Gagal
        |--------------------------------------------------------------------------
        */

        return back()->withErrors([
            'email' => 'Username atau password salah',
        ]);
    }

    /**
     * Logout Admin
     */
    public function destroy(Request $request): RedirectResponse
    {
        session()->forget('admin_logged_in');

        return redirect('/');
    }
}