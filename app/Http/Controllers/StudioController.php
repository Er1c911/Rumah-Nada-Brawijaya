<?php

namespace App\Http\Controllers;

use App\Models\Studio;
use Illuminate\Http\Request;

class StudioController extends Controller
{
    public function index()
    {
        $studio = Studio::all();
        return view('studio.index', compact('studio'));
    }

    public function create()
    {
        return view('studio.create');
    }

    public function store(Request $request)
    {
        Studio::create($request->all());

        return redirect('/');
    }

    public function edit(Studio $studio)
    {
        return view('studio.edit', compact('studio'));
    }

    public function update(Request $request, Studio $studio)
    {
        $studio->update($request->all());

        return redirect('/');
    }

    public function destroy(Studio $studio)
    {
        $studio->delete();

        return redirect('/');
    }
}