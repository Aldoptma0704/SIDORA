<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;

class SuratController extends Controller
{
    public function index()
    {
        $surats = Surat::where('status', 'menunggu')->get();
        return view('pimpinan.surat.index', compact('surats'));
    }

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

    public function disposisi()
    {
        $surats = Surat::where('status', 'disetujui')->get();
        return view('pimpinan.surat.disposisi', compact('surats'));
    }
}
