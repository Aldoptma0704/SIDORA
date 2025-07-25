<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;

class SuratController extends Controller
{
    public function suratMasuk()
    {
        $surats = Surat::where('status_balasan', 'dikirim')->latest()->get();
        return view('admin.surat.masuk', compact('surats'));
    }

    public function lihat($id)
    {
        $surat = Surat::findOrFail($id);
        return view('admin.surat.lihat', compact('surat'));
    }

    public function disposisiMasuk()
    {
        // Ambil semua surat yang punya relasi ke disposisi
        $surats = Surat::whereHas('disposisi')->latest()->get();

        return view('admin.surat.disposisi_masuk', compact('surats'));
    }
}
