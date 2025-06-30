<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Surat;


class dashboardcontroller extends Controller
{
    public function index()
    {
        $surat = Surat::where('user_id', auth()->id())->get();
        return view('pegawai.surat.index', compact('surats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'jenis' => 'required|in:masuk,keluar',
            'isi' => 'required|string'
        ]);

        Surat::create([
            'user_id' => auth()->id(),
            'judul' => $request->judul,
            'jenis' => $request->jenis,
            'isi' => $request->isi,
            'status' => 'menunggu'
        ]);

        return redirect()->route('pegawai.surat.index');
    }

    // Pimpinan\SuratController.php
    public function setujui($id)
    {
        $surat = Surat::findOrFail($id);
        $surat->status = 'disetujui';
        $surat->save();
        return back()->with('success', 'Surat berhasil disetujui.');
    }

    public function tolak($id)
    {
        $surat = Surat::findOrFail($id);
        $surat->status = 'ditolak';
        $surat->save();
        return back()->with('error', 'Surat telah ditolak.');
    }
}
