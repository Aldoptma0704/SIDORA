<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;

class SuratController extends Controller
{
    public function index()
    {
        $surats = Surat::where('user_id', auth()->id())->get();
        return view('pegawai.surat.index', compact('surats'));
    }

    public function create()
    {
        return view('pegawai.surat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'jenis' => 'required|in:masuk,keluar',
            'isi' => 'required',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,png'
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('lampiran_surat', 'public');
        }

        Surat::create([
            'user_id' => auth()->id(),
            'judul' => $request->judul,
            'jenis' => $request->jenis,
            'isi' => $request->isi,
            'status' => 'menunggu',
            'lampiran' => $lampiranPath
        ]);

        return redirect()->route('surat.index');
    }
}
