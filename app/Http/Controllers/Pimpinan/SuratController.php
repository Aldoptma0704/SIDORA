<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;
use Illuminate\Support\Facades\Auth;
use App\Models\Pimpinan;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SuratController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'pimpinan') {
            abort(403, 'Akses hanya untuk pimpinan.');
        }

        // Ambil semua surat dengan jenis 'keluar' atau 'keluar_full', diurutkan berdasarkan tanggal pembuatan terbaru
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
        $surat->perlu_disposisi_admin = true;
        $surat->pengirim_id = auth()->id();
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

    public function buatBalasan(Surat $surat)
    {
        $pimpinan = Pimpinan::first(); // atau sesuai logika
        return view('surat.balas', compact('surat', 'pimpinan'));
    }

    public function simpanSuratBalasan(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'nomor_surat' => 'required|string|max:255',
            'tujuan' => 'required|string',
        ]);

        $validated = $request->only(['judul', 'isi', 'nomor_surat', 'tujuan']);
        $validated['jenis'] = 'balasan'; 
        $validated['status'] = 'draft_pimpinan'; 
        $validated['user_id'] = auth()->id(); 
        $validated['pengirim_id'] = auth()->id();

        Surat::create($validated);

        return redirect()->route('pimpinan.status-surat')->with('success', 'Surat balasan berhasil disimpan sebagai draft.');
    }

    public function statusSuratBalasan()
    {
        $drafts = Surat::where('jenis', 'balasan')
               ->where('user_id', auth()->id())
               ->latest()
               ->get();

        return view('pimpinan.surat.status_surat', compact('drafts'));
    }

    public function kirimSurat($id)
    {
        $surat = Surat::findOrFail($id);

        if ($surat->status !== 'draft_pimpinan') {
            return back()->with('error', 'Hanya draft pimpinan yang bisa dikirim.');
        }

        $surat->status = 'disetujui'; 
        $surat->save();

        return back()->with('success', 'Surat berhasil dikirim dan disetujui.');
    }

    public function view($id)
    {
        $surat = Surat::findOrFail($id);
        $penandatangan = $surat->penandatangan; // pastikan relasi tersedia atau sesuaikan

        return view('pimpinan.surat.lihat', compact('surat', 'penandatangan'));
    }

    /**
     * Menampilkan daftar surat dengan status 'draft'.
     */
    public function draftSurat()
    {
        $drafts = Surat::where('status', 'draft')->get();
        return view('pimpinan.surat.status_surat', compact('drafts'));
    }

    public function showDraftSurat()
    {
        // Ambil hanya surat dengan status 'draft_pimpinan'
        $drafts = Surat::where('status', 'draft_pimpinan')
            ->where('pengirim_id', Auth::id())
            ->get();

        return view('pimpinan.surat.status_surat', compact('drafts'));
    }

    public function destroy($id)
    {
        $surat = Surat::findOrFail($id);
        $surat->delete();

        return redirect()->route('pimpinan.status-surat')
            ->with('success', 'Surat berhasil dihapus.');
    }

    public function edit(Surat $surat)
    {
        // You might add authorization logic here to ensure only the owner or authorized users can edit
        // For example: if (Auth::user()->id !== $surat->user_id) { abort(403); }
        return view('pimpinan.surat.editbalasan', compact('surat'));
    }

    public function update(Request $request, Surat $surat)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            // 'nomor_surat' => 'nullable|string|max:255',
            // 'sifat' => 'nullable|string|max:255',
            // 'lampiran' => 'nullable|string|max:255',
            'tujuan' => 'nullable|string|max:255',
            'isi' => 'required|string',
        ]);

        // Update the Surat model with validated data
        $surat->update($validatedData);

        // Redirect back with a success message
        return redirect()->route('pimpinan.surat.view', $surat->id)->with('success', 'Surat balasan berhasil diperbarui!');
    }

    public function download(Surat $surat)
    {
        // Pastikan pengguna yang terautentikasi saat ini memiliki peran 'pimpinan' untuk mengunduh
        if (Auth::user()->role !== 'pimpinan') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengunduh surat ini.');
        }

        $user = Auth::user();

        // Base64 encode logo utama
        $base64Logo = '';
        $logoPath = public_path('img/logo_lampung.png');

        if (file_exists($logoPath)) {
            $base64Logo = base64_encode(file_get_contents($logoPath));
        } else {
            // Uncomment this line for debugging if the main logo doesn't appear
            // dd('Logo utama tidak ditemukan di: ' . $logoPath);
        }

        $base64Signature = '';
        if ($user && $user->signature) {
            $signaturePath = Storage::disk('public')->path('signatures/' . $user->signature);
            if (file_exists($signaturePath)) {
                $type = pathinfo($signaturePath, PATHINFO_EXTENSION);
                $base64Signature = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($signaturePath));
            } else {
                // Uncomment baris ini untuk debugging jika tanda tangan tidak muncul
                // dd('File tanda tangan tidak ditemukan di: ' . $signaturePath);
            }
        }

        // Base64 encode logo instansi jika ada dan jenis surat adalah 'keluar_full'
        $base64InstansiLogo = '';
        if ($surat->jenis === 'keluar_full' && $surat->logo_instansi) {
            // *** PERBAIKAN PENTING DI SINI ***
            // Karena kolom logo_instansi sudah mengandung 'logos/', kita tidak perlu menambahkannya lagi.
            $instansiLogoPath = Storage::disk('public')->path($surat->logo_instansi); 
            
            if (file_exists($instansiLogoPath)) {
                $type = pathinfo($instansiLogoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($instansiLogoPath);

                // Penting: tambahkan awalan data:image/...;base64,
                $base64InstansiLogo = 'data:image/' . $type . ';base64,' . base64_encode($data);

                // dd('Base64 Instansi Logo: ' . $base64InstansiLogo); // Cek string base64 yang dihasilkan
            } else {
                // dd('File logo instansi tidak ditemukan di: ' . $instansiLogoPath);
            }
        }

        $data = [
            'surat' => $surat,
            'user' => $user,
            'base64Logo' => $base64Logo,
            'base64Signature' => $base64Signature,
            'base64InstansiLogo' => $base64InstansiLogo,
        ];

        // Muat template PDF khusus
        $pdf = Pdf::loadView('pimpinan.surat.pdf_template', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->download('surat_' . Str::slug($surat->judul) . '.pdf');
    }
}
