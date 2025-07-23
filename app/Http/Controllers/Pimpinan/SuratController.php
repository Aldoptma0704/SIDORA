<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;
use Illuminate\Support\Facades\Auth;

class SuratController extends Controller
{

    public function index()
    {
        if (auth()->user()->role !== 'pimpinan') {
            abort(403, 'Akses hanya untuk pimpinan.');
        }

        // Ambil semua surat (bukan hanya yg 'menunggu')
        //$surats = Surat::orderByDesc('created_at')->get();
        $surats = Surat::whereIn('jenis', ['keluar', 'keluar_full'])
            ->orderByDesc('created_at')
            ->get();



        return view('pimpinan.surat.index', compact('surats'));
    }

    public function setujui(Request $request, $id)
    {
        $request->validate([
            'disposisi' => 'required|string|max:255',
        ]);

        $surat = Surat::findOrFail($id);
        $surat->status = 'disetujui';
        $surat->disposisi = $request->disposisi;
        $surat->signed_at = now();

        // Tambahkan ini
        $surat->perlu_disposisi_admin = true;

        $surat->save();

        return redirect()->route('pimpinan.dashboard')->with('success', 'Surat berhasil disetujui dan didisposisikan.');
    }

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
        $penandatangan = auth()->user(); // atau ambil berdasarkan relasi jika beda

        return view('pimpinan.surat.preview', compact('surat', 'penandatangan'));
    }

    public function balasanSurat()
    {
        return view('pimpinan.surat.suratbalasan');
    }

    ////

    public function kirimBalasan(Request $request, $id)
    {
        $request->validate([
            'isi_balasan' => 'required|string',
        ]);

        $surat = Surat::findOrFail($id);

        // Asumsikan kolom 'balasan' sudah tersedia di tabel surat
        $surat->balasan = $request->isi_balasan;
        $surat->status_balasan = 'dikirim';
        $surat->balasan_dikirim_pada = now();
        $surat->save();

        return redirect()->route('pimpinan.surat.index')->with('success', 'Balasan surat berhasil dikirim.');
    }

    public function buatBalasan(Surat $surat)
    {
        $pimpinan = Pimpinan::first(); // atau sesuai logika
        return view('surat.balas', compact('surat', 'pimpinan'));
    }

    public function simpanBalasan(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string',
            'tujuan' => 'required|string',
            'isi' => 'required|string',
            // dan field lain yang dibutuhkan
        ]);

        $validated['jenis'] = 'balasan';
        $validated['status'] = 'disetujui'; // default surat balasan
        $validated['user_id'] = auth()->id();

        Surat::create($validated);

        return redirect()->route('surat.status')->with('success', 'Surat balasan berhasil disimpan.');
    }

    public function simpanSuratBalasan(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'surat_asal_id' => 'nullable|exists:surats,id',
        ]);

        $surat = new Surat();
        $surat->judul = $request->judul;
        $surat->isi = $request->isi;
        $surat->pengirim_id = Auth::id();
        $surat->user_id = Auth::id();
        $surat->jenis = $request->jenis ?? 'keluar'; 
        $surat->is_balasan = true;
        $surat->status_balasan = 'draft';
        $surat->surat_asal_id = $request->surat_asal_id;
        $surat->save();

        return redirect()->back()->with('success', 'Surat balasan berhasil disimpan sebagai draft.');
    }

    public function statusSurat()
    {
        // Ambil semua surat yang statusnya masih draft (misal status = 'draft' atau null)
        $drafts = Surat::where('status', 'draft')
                        ->where('pengirim_id', Auth::id())
                        ->get();

        // return view('pimpinan.status-surat', compact('drafts'));
        return view('pimpinan.surat.status_surat', compact('drafts'));

    }

    public function kirimSurat($id)
    {
        $surat = Surat::findOrFail($id);

        // Pastikan hanya pengirim yang bisa mengirimkan
        if ($surat->pengirim_id != Auth::id()) {
            abort(403);
        }

        // Ubah status surat menjadi 'dikirim'
        // $surat->status = 'dikirim';
        $surat->status_balasan = 'dikirim';
        $surat->save();

        return redirect()->back()->with('success', 'Surat berhasil dikirim ke Admin.');
    }
    
    public function view($id)
    {
        $surat = Surat::findOrFail($id);
        $penandatangan = $surat->penandatangan; // pastikan relasi tersedia atau sesuaikan

        return view('pimpinan.surat.lihat', compact('surat', 'penandatangan'));
    }

    public function draftSurat()
    {
        $drafts = Surat::where('status_balasan', 'draft')->get();
        return view('pimpinan.surat.status_surat', compact('drafts'));
    }
    
    public function showDraftSurat()
    {
        // Ambil hanya surat dengan status_balasan 'draft'
        $drafts = Surat::where('status_balasan', 'draft')
                        ->where('pengirim_id', Auth::id())
                        ->get();

        return view('pimpinan.surat.status_surat', compact('drafts'));
    }


}
