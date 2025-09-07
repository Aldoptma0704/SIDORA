<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
        public function index(Request $request)
    {
        $userId = auth()->id();
        $jenis = $request->query('jenis');

        $query = Surat::query();

        if ($jenis === 'masuk') {
            // Halaman Surat Masuk: Tampilkan surat yang didisposisikan ke user ini (semua status)
            $query->where('disposisi_user_id', $userId);

        } elseif ($jenis === 'keluar') {
            // Halaman Surat Keluar: HANYA tampilkan surat DRAFT yang dibuat oleh user ini
            $query->where('user_id', $userId)
                  ->whereIn('jenis', ['keluar', 'keluar_full'])
                  ->where('status', 'draft'); // <-- Perubahan Kunci di sini

        } else { // Halaman "Semua Surat"
            // Tampilkan surat yang dibuat (tapi hanya draft) ATAU didisposisikan ke user ini (semua status)
            $query->where(function ($q) use ($userId) {
                // Surat Keluar yang masih draft
                $q->where(function($subq) use ($userId) {
                    $subq->where('user_id', $userId)
                         ->where('status', 'draft');
                })
                // ATAU Surat Masuk (disposisi) dengan status apapun
                ->orWhere('disposisi_user_id', $userId);
            });
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
        Log::info('Data Request Surat Store:', $request->all());

        $jenis = $request->input('jenis');

        // Aturan validasi dasar yang berlaku untuk semua jenis
        $rules = [
            'jenis' => 'required|in:keluar,keluar_full',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'nomor_surat' => 'required|string|max:255',
            'sifat' => 'required|string|max:100',
            'lampiran' => 'required|string|max:100',
            'tujuan' => 'required|string',
            'penandatangan_jabatan' => 'required|string|max:255',
            'penandatangan_nama' => 'required|string|max:255',
            'penandatangan_nip' => 'required|string|max:255',
        ];
        if ($request->input('jenis') === 'keluar_full') {
            $rules['nama_instansi'] = 'required|string';
            $rules['alamat_instansi'] = 'required|string';
            $rules['kontak_instansi'] = 'required|string';
            $rules['logo_instansi_file'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
        }
        $validatedData = $request->validate($rules);

        $surat = new Surat();
        $surat->user_id = auth()->id();
        $surat->fill($validatedData);
        $surat->isi = Purifier::clean($validatedData['isi']);
        
        if ($request->input('jenis') === 'keluar_full') {
            $surat->nama_instansi = Purifier::clean($request->nama_instansi);
            $surat->alamat_instansi = Purifier::clean($request->alamat_instansi);
            $surat->kontak_instansi = Purifier::clean($request->kontak_instansi);
            if ($request->hasFile('logo_instansi_file')) {
                $path = $request->file('logo_instansi_file')->store('logos', 'public');
                $surat->logo_instansi = $path;
            }
        }
        
        $surat->status = 'draft'; 
        $surat->save();

        return redirect()->route('pegawai.surat.index')->with('success', 'Draft surat berhasil dibuat!');
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

    /**
     * Update the specified surat in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $surat = Surat::findOrFail($id);
        $jenis = $request->jenis;

        $validatedData = $request->validate([
            'jenis' => 'required|in:keluar,keluar_full',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'nomor_surat' => 'required|string|max:255',
            'sifat' => 'required|string|max:100',
            'lampiran' => 'required|string|max:100',
            'tujuan' => 'required|string',
            'penandatangan_jabatan' => 'required|string|max:255',
            'penandatangan_nama' => 'required|string|max:255',
            'penandatangan_nip' => 'required|string|max:255',
            
            'nama_instansi' => 'nullable|string',
            'alamat_instansi' => 'nullable|string',
            'kontak_instansi' => 'nullable|string',
            'logo_instansi_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $surat->jenis = $validatedData['jenis'];
        $surat->judul = $validatedData['judul'];
        $surat->isi = Purifier::clean($validatedData['isi']);
        $surat->nomor_surat = $validatedData['nomor_surat'];
        $surat->sifat = $validatedData['sifat'];
        $surat->lampiran = $validatedData['lampiran'];
        $surat->tujuan = $validatedData['tujuan'];
        $surat->penandatangan_jabatan = $validatedData['penandatangan_jabatan'];
        $surat->penandatangan_nama = $validatedData['penandatangan_nama'];
        $surat->penandatangan_nip = $validatedData['penandatangan_nip'];
        $surat->status = 'draft'; // Reset status menjadi 'menunggu' setiap kali diupdate

        if ($jenis == 'keluar_full') {
            $surat->nama_instansi = Purifier::clean($request->nama_instansi);
            $surat->alamat_instansi = Purifier::clean($request->alamat_instansi);
            $surat->kontak_instansi = Purifier::clean($request->kontak_instansi);
            
            // Handle file upload untuk logo jika ada
            if ($request->hasFile('logo_instansi_file')) {
                $path = $request->file('logo_instansi_file')->store('logos', 'public');
                $surat->logo_instansi = $path;
            }

        } else {
            $surat->nama_instansi = null;
            $surat->alamat_instansi = null;
            $surat->kontak_instansi = null;
            $surat->logo_instansi = null; // Hapus path logo juga
        }

        $surat->save();

        return redirect()->route('pegawai.surat.preview', $surat->id)->with('success', 'Surat berhasil diperbarui!');
    }

    public function download($id)
    {
        $surat = Surat::findOrFail($id);
        
        $safeFileName = str_replace('/', '-', $surat->nomor_surat);
        
        $pdf = \PDF::loadView('pegawai.surat.pdf_surat', compact('surat'))
                     ->setPaper('A4', 'portrait');
        
        return $pdf->download('surat_' . $safeFileName . '.pdf');
    }
    
    public function statusSurat(Request $request)
    {
        $userId = auth()->id();

        $query = Surat::where('user_id', $userId)
                    ->where('status', '!=', 'draft');

        if ($request->has('search') && $request->search != '') {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $surats = $query->latest()->get();

        $drafts = Surat::where('user_id', $userId)
               ->where('status', 'draft')
               ->latest()
               ->get();

        return view('pegawai.surat.status_surat', [
            'jumlahMenunggu' => Surat::where('user_id', $userId)->where('status', 'menunggu')->count(),
            'jumlahDisetujui' => Surat::where('user_id', $userId)->where('status', 'disetujui')->count(),
            'jumlahDitolak' => Surat::where('user_id', $userId)->where('status', 'ditolak')->count(),
            'surats' => $surats,
            'drafts' => $drafts,
        ]);
    }

    public function ajukan(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:surats,id',
        ]);

        Surat::whereIn('id', $request->ids)
             ->where('user_id', auth()->id())
             ->where('status', 'draft')
             ->update(['status' => 'menunggu']);

        return redirect()->route('pegawai.surat.status')->with('success', 'Surat berhasil diajukan untuk persetujuan.');
    }

    public function destroy($id)
    {
        $surat = Surat::findOrFail($id);

        if ($surat->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus surat ini.');
        }
        if ($surat->status != 'draft') {
             return redirect()->back()->with('error', 'Hanya surat dengan status draft yang bisa dihapus.');
        }
        if ($surat->logo_instansi) {
            Storage::disk('public')->delete($surat->logo_instansi);
        }
        if ($surat->file_path) {
            Storage::disk('public')->delete($surat->file_path);
        }

        $surat->delete();
        return redirect()->route('pegawai.surat.index')->with('success', 'Draft surat berhasil dihapus.');
    }
    
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:surats,id',
        ]);
    
        $suratIds = $request->input('ids');
        $userId = auth()->id();
    
        // hanya ambil surat milik user dan status bukan 'menunggu'
        $suratsToDelete = Surat::where('user_id', $userId)
                               ->whereIn('id', $suratIds)
                               ->whereIn('status', ['disetujui', 'ditolak'])
                               ->get();
    
        if ($suratsToDelete->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada surat yang valid untuk dihapus.');
        }
    
        foreach ($suratsToDelete as $surat) {
            // Hapus file terkait jika ada
            if ($surat->logo_instansi) {
                Storage::disk('public')->delete($surat->logo_instansi);
            }
            if ($surat->file_path) {
                Storage::disk('public')->delete($surat->file_path);
            }
        
            // Hapus record surat dari database
            $surat->delete();
        }
    
        return redirect()->back()->with('success', 'Surat yang dipilih berhasil dihapus.');
    }

    public function uploadPdf(Request $request)
    {
        $request->validate([
            'file_surat' => 'required|mimes:pdf|max:5120',
        ]);

        $file = $request->file('file_surat');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('surat_files', $fileName, 'public');

        $surat = new Surat();
        $surat->user_id = auth()->id();
        $surat->jenis = 'keluar';
        $surat->judul = pathinfo($fileName, PATHINFO_FILENAME);
        $surat->isi = 'File PDF diunggah.';
        $surat->file_path = $filePath;
        $surat->status = 'menunggu';
        $surat->save();

        return redirect()->route('pegawai.surat.index')->with('success', 'File surat berhasil diunggah.');
    }

    public function updateApi(Request $request, $id)
    {
        $surat = Surat::findOrFail($id);

        // update field sesuai kebutuhan
        $surat->update($request->only([
            'perihal',
            'tujuan',
            'isi',
            'tanggal'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Surat berhasil diupdate',
            'data' => $surat
        ]);
    }

}

