<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Surat;

class authcontroller extends Controller
{
    public function login()
    {
        if (auth()->check()) {
            return redirect('/' . auth()->user()->role . '/dashboard');
        }

        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $role = Auth::user()->role;
            return redirect("/$role/dashboard");
        }
        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
