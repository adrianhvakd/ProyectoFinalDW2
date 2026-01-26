<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as AuthFacade;

class Auth extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login()
    {
        $type = 'login';

        return view('auth.index', compact('type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function register()
    {
        $type = 'register';

        return view('auth.index', compact('type'));
    }

    public function logout()
    {
        $type = 'login';
        AuthFacade::logout();

        flash()->use('theme.aurora')->option('timeout', 3000)->success('Cerraste sesión exitosamente');

        return redirect()->route('login');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
