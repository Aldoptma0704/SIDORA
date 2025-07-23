<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

        if ($request->hasFile('signature')) {
            $request->validate([
                'signature' => 'image|mimes:png,jpg,jpeg|max:2048',
            ]);

            if ($user->signature) {
                Storage::delete('public/signatures/' . $user->signature);
            }

            $filename = $user->id . '_signature.' . $request->signature->extension();
            $request->signature->storeAs('public/signatures', $filename);
            $user->signature = $filename;
            $user->save();
        }

        if ($request->hasFile('profile_photo')) {
            $request->validate([
                'profile_photo' => 'image|mimes:png,jpg,jpeg|max:2048',
            ]);

            if ($user->profile_photo) {
                Storage::delete('public/profile_photos/' . $user->profile_photo);
            }

            $filename = $user->id . '_photo.' . $request->profile_photo->extension();
            $request->profile_photo->storeAs('public/profile_photos', $filename);
            $user->profile_photo = $filename;
            $user->save();
        }


        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
}
