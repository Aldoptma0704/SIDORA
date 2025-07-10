<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Log;


class SuratController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $jenis = $request->query('jenis'); // Ambil parameter 'jenis' dari URL

        $query = Surat::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhere('disposisi_user_id', $userId); // Tambahkan surat disposisi
        });

        if ($jenis && in_array($jenis, ['masuk', 'keluar'])) {
            $query->where('jenis', $jenis);
        }

        $surats = $query->latest()->get();

        return view('pegawai.surat.index', [
            'surats' => $surats,
            'jenis' => $jenis
        ]);
    }

    public function create()
    {
        return view('pegawai.surat.create_surat');
    }

    public function store(Request $request)
    {
        // Log isi request
        Log::info('Data Request Surat:', $request->all());

        // Validasi dasar
        $validated = $request->validate([
            'jenis' => 'required|in:masuk,keluar',
            'judul' => 'required|string|max:255', // Judul/Hal
            'isi' => 'required|string',
            'lampiran' => 'required|string|max:100'
        ]);

        // Validasi kondisional untuk surat keluar
        if ($request->jenis == 'keluar') {
            $request->validate([
                'nomor_surat' => 'required|string|max:255',
                'sifat' => 'required|string|max:100',
                'tujuan' => 'required|string',
                'penandatangan_jabatan' => 'required|string',
                'penandatangan_nama' => 'required|string',
                'penandatangan_nip' => 'required|string',
            ]);
        }

        // Buat instance Surat
        $surat = new Surat();
        $surat->user_id = auth()->id();
        $surat->jenis = $request->jenis;
        $surat->judul = $request->judul;
        $surat->isi = Purifier::clean($request->isi);
        $surat->status = 'menunggu';
        $surat->lampiran = $request->lampiran;

        if ($request->jenis == 'keluar') {
            $surat->nomor_surat = $request->nomor_surat;
            $surat->sifat = $request->sifat;
            $surat->tujuan = $request->tujuan;
            $surat->penandatangan_jabatan = $request->penandatangan_jabatan;
            $surat->penandatangan_nama = $request->penandatangan_nama;
            $surat->penandatangan_nip = $request->penandatangan_nip;
        }

        $surat->save();

        return redirect()->route('surat.preview', $surat->id);
    }

    public function preview($id)
    {
        $surat = Surat::findOrFail($id);

        return view('pegawai.surat.preview_surat', compact('surat'));
    }

    public function edit($id)
    {
        $surat = Surat::findOrFail($id);
        return view('pegawai.surat.edit', compact('surat'));
    }

    public function update(Request $request, $id)
    {
        // Temukan surat yang akan diupdate
        $surat = Surat::findOrFail($id);

        // Validasi dasar (sama seperti store, bisa disesuaikan)
        $validated = $request->validate([
            'jenis' => 'required|in:masuk,keluar',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'lampiran' => 'required|string|max:100',
        ]);

        // Validasi kondisional untuk surat keluar
        if ($request->jenis == 'keluar') {
            $request->validate([
                'nomor_surat' => 'required|string|max:255',
                'sifat' => 'required|string|max:100',
                'tujuan' => 'required|string',
                'penandatangan_jabatan' => 'required|string',
                'penandatangan_nama' => 'required|string',
                'penandatangan_nip' => 'required|string',
            ]);
        }

        // Update data surat
        $surat->jenis = $request->jenis;
        $surat->judul = $request->judul;
        $surat->isi = Purifier::clean($request->isi);
        $surat->status = 'menunggu'; // Reset status jika ada perubahan

        if ($request->jenis == 'keluar') {
            $surat->nomor_surat = $request->nomor_surat;
            $surat->sifat = $request->sifat;
            $surat->lampiran = $request->lampiran;
            $surat->tujuan = $request->tujuan;
            $surat->penandatangan_jabatan = $request->penandatangan_jabatan;
            $surat->penandatangan_nama = $request->penandatangan_nama;
            $surat->penandatangan_nip = $request->penandatangan_nip;
        } else {
            // Kosongkan field surat keluar jika jenis diubah ke surat masuk
            $surat->nomor_surat = null;
            $surat->sifat = null;
            // $surat->lampiran = null; // Lampiran tetap ada di validasi dasar
            $surat->tujuan = null;
            $surat->penandatangan_jabatan = null;
            $surat->penandatangan_nama = null;
            $surat->penandatangan_nip = null;
        }

        $surat->save();

        // Redirect kembali ke halaman preview setelah update
        return redirect()->route('surat.preview', $surat->id)->with('success', 'Surat berhasil diperbarui!');
    }

    // Generate PDF
    public function download($id)
    {
        $surat = Surat::findOrFail($id);

        $pdf = \PDF::loadView('pegawai.surat.pdf_surat', compact('surat'))
            ->setPaper('A4', 'portrait');

        return $pdf->download('surat_' . $surat->nomor_surat . '.pdf');
    }

    public function statusSurat(Request $request) // MODIFIED: Tambahkan Request $request
    {
        $userId = auth()->id();

        // MODIFIED: Tambahkan query builder untuk pencarian
        $query = Surat::where('user_id', $userId);

        if ($request->has('search') && $request->search != '') {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $surats = $query->latest()->get();

        return view('pegawai.status_surat', [
            'jumlahMenunggu' => Surat::where('user_id', $userId)->where('status', 'menunggu')->count(),
            'jumlahDisetujui' => Surat::where('user_id', $userId)->where('status', 'disetujui')->count(),
            'jumlahDitolak' => Surat::where('user_id', $userId)->where('status', 'ditolak')->count(),
            'surats' => $surats // Kirim data surat yang sudah difilter
        ]);
    }

    // ADDED: Method baru untuk hapus massal
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:surats,id',
        ]);

        Surat::whereIn('id', $request->ids)->delete();

        return redirect()->route('surat.status_surat')->with('success', 'Surat yang dipilih berhasil dihapus.');
    }

    public function uploadPdf(Request $request)
    {
        // 1. Validasi request
        $request->validate([
            'file_surat' => 'required|mimes:pdf|max:5120', // Hanya PDF, maks 5MB
        ]);

        // 2. Simpan file
        $file = $request->file('file_surat');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('surat_files', $fileName, 'public'); // Simpan di storage/app/public/surat_files

        // 3. Buat record surat baru di database
        $surat = new Surat();
        $surat->user_id = auth()->id();
        $surat->jenis = 'masuk'; // Default jenis surat 'masuk' untuk file yang di-upload
        $surat->judul = pathinfo($fileName, PATHINFO_FILENAME); // Judul diambil dari nama file
        $surat->isi = 'File PDF diunggah.'; // Isi default
        $surat->file_path = $filePath;
        $surat->status = 'menunggu'; // Status default
        $surat->save();

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->route('surat.index')->with('success', 'File surat berhasil diunggah.');
    }
}
