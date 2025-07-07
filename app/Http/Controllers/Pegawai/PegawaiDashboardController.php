<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;

class PegawaiDashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        return view('pegawai.surat.pegawaiDashboard', [
            'countMenunggu' => Surat::where('user_id', $userId)->where('status', 'menunggu')->count(),
            'countDisetujui' => Surat::where('user_id', $userId)->where('status', 'disetujui')->count(),
            'countDitolak' => Surat::where('user_id', $userId)->where('status', 'ditolak')->count(),
        ]);
    }

    public function statusSurat()
    {
        $userId = auth()->id();

        return view('pegawai.surat.status_surat', [
            'jumlahMenunggu' => Surat::where('user_id', $userId)->where('status', 'menunggu')->count(),
            'jumlahDisetujui' => Surat::where('user_id', $userId)->where('status', 'disetujui')->count(),
            'jumlahDitolak' => Surat::where('user_id', $userId)->where('status', 'ditolak')->count(),
            'surats' => Surat::where('user_id', $userId)->latest()->get()
        ]);
    }
}
