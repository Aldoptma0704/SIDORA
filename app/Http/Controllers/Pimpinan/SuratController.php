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
    /**
     * Menampilkan daftar surat masuk atau keluar untuk pimpinan.
     * Filter berdasarkan jenis 'keluar' atau 'keluar_full'.
     */
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

    /**
     * Menyetujui surat dan menambahkan disposisi.
     */
    public function setujui(Request $request, $id)
    {
        $request->validate([
            'disposisi' => 'required|string|max:255',
        ]);

        $surat = Surat::findOrFail($id);
        $surat->status = 'disetujui';
        $surat->disposisi = $request->disposisi;
        $surat->signed_at = now();

        // Menandai bahwa surat perlu disposisi admin
        $surat->perlu_disposisi_admin = true;

        $surat->save();

        return redirect()->route('pimpinan.dashboard')->with('success', 'Surat berhasil disetujui dan didisposisikan.');
    }

    /**
     * Menampilkan daftar surat yang telah disetujui dan memiliki disposisi.
     */
    public function disposisi()
    {
        $surats = Surat::where('status', 'disetujui')
            ->whereNotNull('disposisi')
            ->orderBy('disposisi')
            ->get();

        return view('pimpinan.surat.disposisi', compact('surats'));
    }

    /**
     * Menolak surat dengan alasan.
     */
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

    /**
     * Menampilkan pratinjau surat.
     */
    public function preview($id)
    {
        if (auth()->user()->role !== 'pimpinan') {
            abort(403, 'Akses ditolak.');
        }

        $surat = Surat::findOrFail($id);
        $penandatangan = auth()->user(); // atau ambil berdasarkan relasi jika beda

        return view('pimpinan.surat.preview', compact('surat', 'penandatangan'));
    }

    /**
     * Menampilkan halaman untuk membuat balasan surat.
     */
    public function balasanSurat()
    {
        return view('pimpinan.surat.suratbalasan');
    }

    /**
     * Mengirim balasan surat.
     */
    public function kirimBalasan(Request $request, $id)
    {
        $request->validate([
            'isi_balasan' => 'required|string',
        ]);

        $surat = Surat::findOrFail($id);

        // Asumsikan kolom 'balasan' sudah tersedia di tabel surat
        $surat->balasan = $request->isi_balasan;
        $surat->status_balasan = 'dikirim'; // Menggunakan status_balasan
        $surat->balasan_dikirim_pada = now();
        $surat->save();

        return redirect()->route('pimpinan.surat.index')->with('success', 'Balasan surat berhasil dikirim.');
    }

    /**
     * Menampilkan formulir untuk membuat balasan surat.
     */
    public function buatBalasan(Surat $surat)
    {
        $pimpinan = Pimpinan::first(); // atau sesuai logika
        return view('surat.balas', compact('surat', 'pimpinan'));
    }

    /**
     * Menyimpan surat balasan baru.
     */
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

    /**
     * Menyimpan surat balasan sebagai draft.
     */
    public function simpanSuratBalasan(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            //'surat_asal_id' => 'nullable|exists:surats,id',
        ]);

        $surat = new Surat();
        $surat->judul = $request->judul;
        $surat->isi = $request->isi;
        $surat->pengirim_id = Auth::id();
        $surat->user_id = Auth::id();
        $surat->jenis = $request->jenis ?? 'keluar';
        $surat->status = 'draft_pimpinan';
        // $surat->is_balasan = true;
        // $surat->status_balasan = 'draft';
        // $surat->surat_asal_id = $request->surat_asal_id;
        $surat->save();

        return redirect()->back()->with('success', 'Surat balasan berhasil disimpan sebagai draft.');
    }

    /**
     * Menampilkan status surat yang dikirim oleh pengguna saat ini.
     * Mengambil surat dengan status 'draft_pimpinan' atau 'dikirim'.
     */
    public function statusSurat()
    {
        // Mengambil semua surat yang statusnya 'draft_pimpinan' atau 'dikirim'
        // dan dikirim oleh pengguna yang sedang login.
        $drafts = Surat::whereIn('status', ['draft_pimpinan', 'dikirim'])
            ->where('pengirim_id', Auth::id())
            ->get();

        return view('pimpinan.surat.status_surat', compact('drafts'));
    }

    /**
     * Mengubah status surat menjadi 'dikirim'.
     */
    public function kirimSurat($id)
    {
        $surat = Surat::findOrFail($id);

        // Pastikan hanya pengirim yang bisa mengirimkan
        if ($surat->pengirim_id != Auth::id()) {
            abort(403);
        }

        // Ubah status surat menjadi 'dikirim'
        $surat->status = 'dikirim';
        $surat->save();

        return redirect()->back()->with('success', 'Surat berhasil dikirim ke Admin.');
    }

    /**
     * Menampilkan detail surat.
     */
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

    /**
     * Menampilkan daftar surat dengan status 'draft_pimpinan'.
     * Catatan: Jika Anda ingin menggabungkan ini dengan statusSurat(), Anda bisa menghapus fungsi ini.
     */
    public function showDraftSurat()
    {
        // Ambil hanya surat dengan status 'draft_pimpinan'
        $drafts = Surat::where('status', 'draft_pimpinan')
            ->where('pengirim_id', Auth::id())
            ->get();

        return view('pimpinan.surat.status_surat', compact('drafts'));
    }

    /**
     * Menghapus surat.
     */
    public function destroy($id)
    {
        $surat = Surat::findOrFail($id);

        if ($surat->status !== 'draft_pimpinan') {
            return redirect()->back()->with('error', 'Surat hanya bisa dihapus jika masih berstatus draft.');
        }

        $surat->delete();
        return redirect()->back()->with('success', 'Surat berhasil dihapus.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Surat  $surat
     * @return \Illuminate\Http\Response
     */
    public function edit(Surat $surat)
    {
        // You might add authorization logic here to ensure only the owner or authorized users can edit
        // For example: if (Auth::user()->id !== $surat->user_id) { abort(403); }
        return view('pimpinan.surat.editbalasan', compact('surat'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Surat  $surat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Surat $surat)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'nomor_surat' => 'nullable|string|max:255',
            'sifat' => 'nullable|string|max:255',
            'lampiran' => 'nullable|string|max:255',
            'tujuan' => 'nullable|string|max:255',
            'jenis' => 'required|in:biasa,keluar_full',
            'nama_instansi' => 'nullable|string|max:255',
            'kontak_instansi' => 'nullable|string|max:255',
            'alamat_instansi' => 'nullable|string',
            'logo_instansi' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
            'isi' => 'required|string',
        ]);

        // Handle logo upload if a new one is provided
        if ($request->hasFile('logo_instansi')) {
            // Delete old logo if it exists
            if ($surat->logo_instansi) {
                Storage::disk('public')->delete('logo/' . $surat->logo_instansi);
            }
            // Store new logo
            $logoPath = $request->file('logo_instansi')->store('logo', 'public');
            $validatedData['logo_instansi'] = basename($logoPath);
        }

        // Update the Surat model with validated data
        $surat->update($validatedData);

        // Redirect back with a success message
        return redirect()->route('pimpinan.surat.view', $surat->id)->with('success', 'Surat balasan berhasil diperbarui!');
    }
    /**
     * Generate and download the PDF for the specified surat.
     *
     * @param  \App\Models\Surat  $surat
     * @return \Illuminate\Http\Response
     */
    public function download(Surat $surat)
    {
        // Pastikan pengguna yang terautentikasi saat ini memiliki peran 'pimpinan' untuk mengunduh
        if (Auth::user()->role !== 'pimpinan') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengunduh surat ini.');
        }

        $user = Auth::user();

        // // Base64 encode logo utama
        // $base64Logo = '';
        // $logoPath = public_path('img/logo_lampung.png');
        // if (file_exists($logoPath)) {
        //     $type = pathinfo($logoPath, PATHINFO_EXTENSION);
        //     $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($logoPath));
        // } else {
        //     // Uncomment baris ini untuk debugging jika logo utama tidak muncul
        //     // dd('Logo utama tidak ditemukan di: ' . $logoPath);
        // }

        // Base64 encode logo utama
        $base64Logo = '';
        $logoPath = public_path('img/logo_lampung.png');

        if (file_exists($logoPath)) {
            $base64Logo = base64_encode(file_get_contents($logoPath));
        } else {
            // Uncomment this line for debugging if the main logo doesn't appear
            // dd('Logo utama tidak ditemukan di: ' . $logoPath);
        }

        // Pass $base64Logo to your view
        // return view('surat.download', compact('base64Logo'));

        // Base64 encode gambar tanda tangan
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
            
            // --- DEBUGGING LOGO INSTANSI ---
            // Uncomment baris di bawah ini satu per satu untuk debugging:
            // dd('Path logo instansi: ' . $instansiLogoPath); // Cek apakah path sudah benar
            
            if (file_exists($instansiLogoPath)) {
                $type = pathinfo($instansiLogoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($instansiLogoPath);
                
                // if (empty($data)) {
                //     dd('Isi file logo instansi kosong atau tidak bisa dibaca: ' . $instansiLogoPath);
                // }

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
