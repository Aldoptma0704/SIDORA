<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

    public function showResetPasswordForm()
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'old_password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:6'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Username atau password lama salah']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('login')->with('success', 'Password berhasil diganti. Silakan login.');
    }
}
