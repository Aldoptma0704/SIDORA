<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SuratController extends Controller
{
    public function suratMasuk()
    {
        // Ambil semua surat masuk dari instansi lain
        $surats = Surat::where('jenis', 'masuk')
            ->with(['disposisiKepada', 'user'])
            ->latest()
            ->get();

        // Ambil semua user (pegawai, pimpinan kepala bidang, kepala sekretariat, dan kepala dinas) untuk pilihan tujuan surat
        $pegawais = User::whereIn('role', ['pegawai', 'pimpinan'])
            ->orderByRaw("FIELD(role, 'pimpinan', 'pegawai')")
            ->orderBy('name')
            ->get();

        return view('admin.surat.masuk', compact('surats', 'pegawais'));
    }

    public function storeSuratMasuk(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'nama_instansi' => 'required|string|max:255',
            'nomor_surat' => 'nullable|string|max:100',
            'sifat' => 'nullable|string|max:50',
            'lampiran' => 'nullable|string|max:100',
            'isi' => 'nullable|string',
            'file_surat' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'pegawai_id' => 'nullable|exists:users,id',
        ]);

        $filePath = $request->file('file_surat')->store('surat_masuk', 'public');

        $surat = new Surat();
        $surat->user_id = Auth::id(); // diunggah oleh admin
        $surat->jenis = 'masuk';
        $surat->judul = $request->judul;
        $surat->nama_instansi = $request->nama_instansi;
        $surat->nomor_surat = $request->nomor_surat;
        $surat->sifat = $request->sifat ?: 'Biasa';
        $surat->lampiran = $request->lampiran ?: '-';
        $surat->isi = $request->isi ?: ('Surat masuk dari ' . $request->nama_instansi . ' perihal ' . $request->judul);
        $surat->file_path = $filePath;

        if ($request->filled('pegawai_id')) {
            $pegawai = User::findOrFail($request->pegawai_id);
            $surat->disposisi_user_id = $pegawai->id;
            $surat->tujuan = $pegawai->name . ' (' . $pegawai->email . ')';
            $surat->status = 'dikirim';
        } else {
            $surat->status = 'draft';
        }

        $surat->save();

        $pesan = 'Surat masuk berhasil diunggah' . ($surat->disposisi_user_id ? ' dan langsung dikirim ke ' . $surat->tujuan : '') . '.';
        return redirect()->route('admin.surat.masuk')->with('success', $pesan);
    }

    public function kirimSuratMasuk(Request $request, $id)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:users,id',
        ]);

        $surat = Surat::where('jenis', 'masuk')->findOrFail($id);
        $pegawai = User::findOrFail($request->pegawai_id);

        $surat->disposisi_user_id = $pegawai->id;
        $surat->tujuan = $pegawai->name . ' (' . $pegawai->email . ')';
        $surat->status = 'dikirim';
        $surat->save();

        return redirect()->route('admin.surat.masuk')->with('success', 'Surat masuk berhasil dikirim ke ' . $pegawai->name . ' (' . $pegawai->email . ').');
    }

    public function lihatSuratMasuk($id)
    {
        $surat = Surat::where('jenis', 'masuk')->with(['disposisiKepada', 'user'])->findOrFail($id);
        return view('admin.surat.masuk_detail', compact('surat'));
    }

    public function destroySuratMasuk($id)
    {
        $surat = Surat::where('jenis', 'masuk')->findOrFail($id);

        if ($surat->file_path && Storage::disk('public')->exists($surat->file_path)) {
            Storage::disk('public')->delete($surat->file_path);
        }

        $surat->delete();

        return redirect()->back()->with('success', 'Surat masuk berhasil dihapus.');
    }

    public function bulkDestroySuratMasuk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:surats,id',
        ]);

        $suratsToDelete = Surat::where('jenis', 'masuk')
            ->whereIn('id', $request->input('ids'))
            ->get();

        if ($suratsToDelete->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada surat masuk yang valid untuk dihapus.');
        }

        foreach ($suratsToDelete as $surat) {
            if ($surat->file_path && Storage::disk('public')->exists($surat->file_path)) {
                Storage::disk('public')->delete($surat->file_path);
            }
            $surat->delete();
        }

        return redirect()->back()->with('success', count($suratsToDelete) . ' surat masuk berhasil dihapus.');
    }

    public function lihat(Request $request, $id)
    {
        $surat = Surat::with(['penandatangans', 'user', 'pengirim'])->findOrFail($id);

        // Pastikan hanya admin atau pemilik surat yang bisa melihat
        if (Auth::user()->role !== 'admin' && Auth::user()->id !== $surat->user_id) {
            abort(403, 'Akses ditolak.');
        }

        $nextNomorUrut = self::getNextNomorUrut(date('Y'));
        $klasifikasiList = self::getKlasifikasiArsip();

        return view('admin.surat.lihat', compact('surat', 'nextNomorUrut', 'klasifikasiList'));
    }

    public function download(Surat $surat)
    {
        // Pastikan hanya admin yang bisa mengunduh
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengunduh surat ini.');
        }

        // Jika surat merupakan file PDF yang diunggah
        if ($surat->file_path && Storage::disk('public')->exists($surat->file_path)) {
            return Storage::disk('public')->download($surat->file_path, 'surat_' . Str::slug($surat->judul) . '.pdf');
        }

        // Jika surat memiliki struktur penandatangan atau surat keluar resmi
        if ($surat->penandatangans()->exists() || in_array($surat->jenis, ['keluar', 'keluar_full'])) {
            $surat->load(['penandatangans.user', 'user', 'pengirim']);
            $safeFileName = str_replace('/', '-', $surat->nomor_surat ?: Str::slug($surat->judul));
            $pdf = Pdf::loadView('pegawai.surat.pdf_surat', compact('surat'))
                      ->setPaper('A4', 'portrait');
            return $pdf->download('surat_' . $safeFileName . '.pdf');
        }

        // Menggunakan pengirim surat sebagai penandatangan untuk tampilan fallback
        $penandatangan = $surat->pengirim;

        // Base64 encode the main logo
        $base64Logo = '';
        $logoPath = public_path('img/logo_lampung.png');
        if (file_exists($logoPath)) {
            $base64Logo = base64_encode(file_get_contents($logoPath));
        }

        // Base64 encode the signature image
        $base64Signature = '';
        if ($penandatangan && $penandatangan->signature) {
            $signaturePath = Storage::disk('public')->path('signatures/' . $penandatangan->signature);
            if (file_exists($signaturePath)) {
                $base64Signature = base64_encode(file_get_contents($signaturePath));
            }
        }

        // Base64 encode the institution logo if it exists
        $base64InstansiLogo = '';
        if ($surat->jenis === 'keluar_full' && $surat->logo_instansi) {
            $instansiLogoPath = Storage::disk('public')->path('logo/' . $surat->logo_instansi);
            if (file_exists($instansiLogoPath)) {
                $base64InstansiLogo = base64_encode(file_get_contents($instansiLogoPath));
            }
        }

        $data = [
            'surat' => $surat,
            'penandatangan' => $penandatangan,
            'base64Logo' => $base64Logo, // Pass base64 encoded main logo
            'base64Signature' => $base64Signature, // Pass base64 encoded signature
            'base64InstansiLogo' => $base64InstansiLogo, // Pass base64 encoded institution logo
        ];

        $pdf = Pdf::loadView('admin.surat.pdf_template', $data); 
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]); 

        return $pdf->download('surat_' . Str::slug($surat->judul) . '.pdf');
    }

    /**
     * Generate nomor surat otomatis sesuai Pergub Lampung No. 14 & 16 Th 2023
     * Format: {kode_klasifikasi}/{nomor_urut}/{kode_unit}/{bulan}/{tahun}
     * Contoh: 500.15.6.1/0001/N.08/10/2026
     */
    public function generateNomor(Request $request, $id)
    {
        $request->validate([
            'kode_klasifikasi' => 'required|string|max:50',
            'nomor_urut'       => 'required|string|max:20',
            'kode_unit'        => 'required|string|max:20',
            'bulan'            => 'required|string|max:10',
            'tahun'            => 'required|string|max:10',
        ]);

        $surat = Surat::findOrFail($id);

        $kodeKlasifikasi = trim($request->kode_klasifikasi);
        // Pastikan nomor urut minimal 4 digit (misal: 0001)
        $nomorUrutRaw    = trim($request->nomor_urut);
        $nomorUrut       = is_numeric($nomorUrutRaw) ? str_pad($nomorUrutRaw, 4, '0', STR_PAD_LEFT) : $nomorUrutRaw;
        $kodeUnit        = trim($request->kode_unit);
        $bulan           = str_pad(trim($request->bulan), 2, '0', STR_PAD_LEFT);
        $tahun           = trim($request->tahun);

        $nomorSurat = "{$kodeKlasifikasi}/{$nomorUrut}/{$kodeUnit}/{$bulan}/{$tahun}";

        $surat->nomor_surat = $nomorSurat;
        $surat->save();

        return back()->with('success', "Nomor surat resmi berhasil di-generate: {$nomorSurat}");
    }

    /**
     * Dapatkan nomor urut surat berikutnya untuk tahun berjalan (4 digit zero-padded)
     */
    public static function getNextNomorUrut($tahun = null)
    {
        $tahun = $tahun ?: date('Y');
        $surats = Surat::whereNotNull('nomor_surat')
            ->where('nomor_surat', '!=', '')
            ->whereYear('created_at', $tahun)
            ->pluck('nomor_surat');

        $maxUrut = 0;
        foreach ($surats as $ns) {
            $parts = explode('/', $ns);
            if (count($parts) >= 2) {
                if (is_numeric($parts[1])) {
                    $val = (int) $parts[1];
                    if ($val > $maxUrut) {
                        $maxUrut = $val;
                    }
                }
            }
        }

        return str_pad($maxUrut + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Daftar Kode Klasifikasi Arsip Pemprov Lampung (Pergub No. 16 Th 2023)
     */
    public static function getKlasifikasiArsip()
    {
        return [
            ['kode' => '500.15.6.1', 'nama' => 'Pemagangan Dalam Negeri (Bina Pemagangan)'],
            ['kode' => '500.15.6.2', 'nama' => 'Pemagangan Luar Negeri (Bina Pemagangan)'],
            ['kode' => '500.15.6.3', 'nama' => 'Perizinan dan Advokasi Pemagangan'],
            ['kode' => '500.15.6.4', 'nama' => 'Promosi dan Jenjang Pemagangan'],
            ['kode' => '500.15.6',   'nama' => 'Bina Pemagangan (Umum)'],
            ['kode' => '500.15.1',   'nama' => 'Perencanaan Tenaga Kerja'],
            ['kode' => '500.15.2',   'nama' => 'Penempatan Tenaga Kerja dan Perluasan Kesempatan Kerja'],
            ['kode' => '500.15.3',   'nama' => 'Pelatihan Kerja dan Sertifikasi'],
            ['kode' => '500.15.4',   'nama' => 'Standardisasi Kompetensi Kerja'],
            ['kode' => '500.15.5',   'nama' => 'Produktivitas Tenaga Kerja'],
            ['kode' => '500.15.7',   'nama' => 'Hubungan Industrial dan Syarat Kerja'],
            ['kode' => '500.15.8',   'nama' => 'Pengupahan dan Jaminan Sosial Tenaga Kerja'],
            ['kode' => '500.15.9',   'nama' => 'Pengawasan Ketenagakerjaan dan K3'],
            ['kode' => '500.15',     'nama' => 'Ketenagakerjaan (Umum)'],
            ['kode' => '800',        'nama' => 'Kepegawaian (Surat Tugas / Cuti / Disiplin / Mutasi)'],
            ['kode' => '000',        'nama' => 'Umum / Tata Usaha / Fasilitatif'],
        ];
    }

    public function destroySurat($id)
    {
        $surat = Surat::findOrFail($id);

        if ($surat->logo_instansi) {
            Storage::disk('public')->delete($surat->logo_instansi);
        }
        if ($surat->file_path && Storage::disk('public')->exists($surat->file_path)) {
            Storage::disk('public')->delete($surat->file_path);
        }
        foreach ($surat->penandatangans as $ttd) {
            if ($ttd->tanda_tangan && !str_contains($ttd->tanda_tangan, '_signature.')) {
                Storage::disk('public')->delete($ttd->tanda_tangan);
            }
        }

        $surat->delete();

        return redirect()->back()->with('success', 'Surat berhasil dihapus.');
    }

    public function bulkDestroySurat(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:surats,id',
        ]);

        $suratIds = $request->input('ids');

        $suratsToDelete = Surat::whereIn('id', $suratIds)->get();

        if ($suratsToDelete->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada surat yang valid untuk dihapus.');
        }

        foreach ($suratsToDelete as $surat) {
            if ($surat->logo_instansi) {
                Storage::disk('public')->delete($surat->logo_instansi);
            }
            if ($surat->file_path && Storage::disk('public')->exists($surat->file_path)) {
                Storage::disk('public')->delete($surat->file_path);
            }
            foreach ($surat->penandatangans as $ttd) {
                if ($ttd->tanda_tangan && !str_contains($ttd->tanda_tangan, '_signature.')) {
                    Storage::disk('public')->delete($ttd->tanda_tangan);
                }
            }
            $surat->delete();
        }

        return redirect()->back()->with('success', count($suratsToDelete) . ' surat berhasil dihapus.');
    }
}
