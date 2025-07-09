<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;

class SuratController extends Controller
{
    // public function index()
    // {
    //     $surats = Surat::where('status', 'menunggu')->get();
    //     return view('pimpinan.surat.index', compact('surats'));
    // }
    public function index()
    {
        // Ambil semua surat (bukan hanya yg 'menunggu')
        $surats = Surat::orderByDesc('created_at')->get();

        return view('pimpinan.surat.index', compact('surats'));
    }


    // public function setujui($id)
    // {
    //     if (auth()->user()->role !== 'pimpinan') {
    //         abort(403, 'Akses ditolak.');
    //     }

    //     $surat = Surat::findOrFail($id);
    //     $surat->status = 'disetujui';
    //     $surat->signed_at = now(); 
    //     $surat->save();

    //     return back()->with('success', 'Surat berhasil disetujui.');
    // }
    public function setujui(Request $request, $id)
    {
        $request->validate([
            'disposisi' => 'required|string|max:255',
        ]);

        $surat = Surat::findOrFail($id);
        $surat->status = 'disetujui';
        $surat->disposisi = $request->disposisi;
        $surat->signed_at = now();
        $surat->save();

        return redirect()->route('pimpinan.dashboard')->with('success', 'Surat berhasil disetujui dan didisposisikan.');
    }


    // public function disposisi()
    // {
    //     $surats = Surat::where('status', 'disetujui')->get();
    //     return view('pimpinan.surat.disposisi', compact('surats'));
    // }
    
    public function disposisi()
    {
        $surats = Surat::where('status', 'disetujui')
                    ->whereNotNull('disposisi')
                    ->orderBy('disposisi')
                    ->get();

        return view('pimpinan.surat.disposisi', compact('surats'));
    }


    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:255',
        ]);

        $surat = Surat::findOrFail($id);
        $surat->status = 'ditolak';
        $surat->alasan_penolakan = $request->alasan_penolakan;
        $surat->save();

        return redirect()->back()->with('success', 'Surat berhasil ditolak dengan alasan.');
    }



    public function preview($id)
    {
        if (auth()->user()->role !== 'pimpinan') {
            abort(403, 'Akses ditolak.');
        }

        $surat = Surat::findOrFail($id);
        return view('pimpinan.surat.preview', compact('surat'));
    }


}
