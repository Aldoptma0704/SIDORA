<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;
use App\Models\User;

class DisposisiController extends Controller
{
    public function form()
    {
        $surats = Surat::where('status', 'disetujui')->get();
        $pegawais = User::where('role', 'pegawai')->get();

        return view('admin.disposisi', compact('surats', 'pegawais'));
    }

    public function kirim(Request $request)
    {
        $request->validate([
            'surat_id' => 'required|exists:surats,id',
            'disposisi_user_id' => 'required|exists:users,id',
        ]);

        $surat = Surat::findOrFail($request->surat_id);
        $surat->disposisi_user_id = $request->disposisi_user_id;
        $surat->save();

        return back()->with('success', 'Surat berhasil didisposisikan.');
    }
}
