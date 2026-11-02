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

        $countMenunggu = Surat::where('user_id', $userId)->where('status', 'menunggu')->count();
        $countDisetujui = Surat::where('user_id', $userId)->where('status', 'disetujui')->count();
        $countDitolak = Surat::where('user_id', $userId)->where('status', 'ditolak')->count();
        $totalSurat = $countMenunggu + $countDisetujui + $countDitolak;

        $recentSurats = Surat::where('user_id', $userId)
            ->latest()
            ->take(6)
            ->get();

        $approvalRate = $totalSurat > 0 ? round(($countDisetujui / $totalSurat) * 100, 1) : 0;

        return view('pegawai.surat.pegawaiDashboard', compact(
            'countMenunggu',
            'countDisetujui',
            'countDitolak',
            'totalSurat',
            'recentSurats',
            'approvalRate'
        ));
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
