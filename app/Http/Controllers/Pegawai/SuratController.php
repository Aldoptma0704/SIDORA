<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;
use App\Models\SuratPenandatangan;
use App\Models\User;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $jenis = $request->filled('jenis') ? $request->query('jenis') : null;

        $query = Surat::query();

        if ($jenis === 'masuk') {
            // Halaman Surat Masuk: Tampilkan surat yang didisposisikan ke user ini ATAU surat masuk yang diunggah oleh user ini
            $query->where(function($q) use ($userId) {
                $q->where('disposisi_user_id', $userId)
                  ->orWhere(function($subq) use ($userId) {
                      $subq->where('user_id', $userId)
                           ->where('jenis', 'masuk');
                  });
            });

        } elseif ($jenis === 'keluar') {
            // Halaman Surat Keluar: Tampilkan surat keluar dengan status draft, disetujui, dan ditolak
            $query->where('user_id', $userId)
                  ->whereIn('jenis', ['keluar', 'keluar_full'])
                  ->whereIn('status', ['draft', 'disetujui', 'ditolak']);

        } else { // Halaman "Semua Surat"
            // Tampilkan surat keluar user (draft, disetujui, ditolak) ATAU surat masuk untuk user ini
            $query->where(function ($q) use ($userId) {
                $q->where(function($sub) use ($userId) {
                    $sub->where('user_id', $userId)
                        ->whereIn('status', ['draft', 'disetujui', 'ditolak']);
                })->orWhere(function($sub) use ($userId) {
                    $sub->where('disposisi_user_id', $userId);
                });
            });
        }

        $surats = $query->latest()->get();

        // Hitung total untuk badge counter pada tab navigation
        $countSemua = Surat::where(function ($q) use ($userId) {
            $q->where(function($sub) use ($userId) {
                $sub->where('user_id', $userId)
                    ->whereIn('status', ['draft', 'disetujui', 'ditolak']);
            })->orWhere(function($sub) use ($userId) {
                $sub->where('disposisi_user_id', $userId);
            });
        })->count();

        $countMasuk = Surat::where(function($q) use ($userId) {
            $q->where('disposisi_user_id', $userId)
              ->orWhere(function($subq) use ($userId) {
                  $subq->where('user_id', $userId)
                       ->where('jenis', 'masuk');
              });
        })->count();

        $countKeluar = Surat::where('user_id', $userId)
            ->whereIn('jenis', ['keluar', 'keluar_full'])
            ->whereIn('status', ['draft', 'disetujui', 'ditolak'])
            ->count();

        return view('pegawai.surat.index', [
            'surats' => $surats,
            'jenis' => $jenis,
            'countSemua' => $countSemua,
            'countMasuk' => $countMasuk,
            'countKeluar' => $countKeluar
        ]);
    }

    public function create()
    {
        $pimpinanUsers = User::where('role', 'pimpinan')->get();
        return view('pegawai.surat.create_surat', compact('pimpinanUsers'));
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
            'nomor_surat' => 'nullable|string|max:255',
            'sifat' => 'required|string|max:100',
            'lampiran' => 'required|string|max:100',
            'tujuan' => 'required|string',
            'penandatangan' => 'nullable|array',
            'penandatangan.*.peran' => 'nullable|string|max:50',
            'penandatangan.*.user_id' => 'nullable',
            'penandatangan.*.urutan' => 'nullable|integer',
            'penandatangan.*.jabatan' => 'nullable|string|max:255',
            'penandatangan.*.nama' => 'nullable|string|max:255',
            'penandatangan.*.nip' => 'nullable|string|max:255',
            'penandatangan.*.file_ttd' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'file_ttd_pembuat' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'penandatangan_jabatan' => 'nullable|string|max:255',
            'penandatangan_nama' => 'nullable|string|max:255',
            'penandatangan_nip' => 'nullable|string|max:255',
        ];
        if ($request->input('jenis') === 'keluar_full') {
            $rules['nama_instansi'] = 'required|string';
            $rules['alamat_instansi'] = 'required|string';
            $rules['kontak_instansi'] = 'required|string';
            $rules['email_instansi'] = 'nullable|string|max:255';
            $rules['website_instansi'] = 'nullable|string|max:255';
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
            
            $telepon = Purifier::clean($request->kontak_instansi ?? '');
            $email = Purifier::clean($request->email_instansi ?? '');
            $website = Purifier::clean($request->website_instansi ?? '');

            if (\Illuminate\Support\Facades\Schema::hasColumn('surats', 'email_instansi') && \Illuminate\Support\Facades\Schema::hasColumn('surats', 'website_instansi')) {
                $surat->kontak_instansi = $telepon;
                $surat->email_instansi = $email;
                $surat->website_instansi = $website;
            } else {
                if (!empty($email) || !empty($website)) {
                    $surat->kontak_instansi = json_encode([
                        'telepon' => $telepon,
                        'email' => $email,
                        'website' => $website,
                    ]);
                } else {
                    $surat->kontak_instansi = $telepon;
                }
            }

            if ($request->hasFile('logo_instansi_file')) {
                $path = $request->file('logo_instansi_file')->store('logos', 'public');
                $surat->logo_instansi = $path;
            }
        }

        // Sinkronisasi kolom penandatangan legacy
        $penandatanganList = $request->input('penandatangan', []);
        if (!empty($penandatanganList)) {
            $primary = end($penandatanganList);
            $surat->penandatangan_jabatan = $primary['jabatan'] ?? ($request->penandatangan_jabatan ?? '-');
            $surat->penandatangan_nama = $primary['nama'] ?? ($request->penandatangan_nama ?? '-');
            $surat->penandatangan_nip = $primary['nip'] ?? ($request->penandatangan_nip ?? '-');
        }
        
        $surat->status = 'draft'; 
        $surat->save();

        // Simpan multiple penandatangan ke tabel relasi
        if (!empty($penandatanganList)) {
            foreach ($penandatanganList as $index => $item) {
                if (empty($item['nama']) && empty($item['jabatan'])) continue;
                $peran = ($item['peran'] ?? 'pimpinan') === 'pembuat' ? 'pembuat' : 'pimpinan';
                $ttd = new SuratPenandatangan();
                $ttd->surat_id = $surat->id;
                $ttd->user_id = !empty($item['user_id']) ? $item['user_id'] : ($peran === 'pembuat' ? auth()->id() : null);
                $ttd->peran = $peran;
                $ttd->urutan = $item['urutan'] ?? ($index + 1);
                $ttd->jabatan = $item['jabatan'] ?? null;
                $ttd->nama = $item['nama'] ?? '';
                $ttd->nip = $item['nip'] ?? null;

                if ($peran === 'pembuat') {
                    $ttd->status = 'ditandatangani';
                    $ttd->signed_at = now();
                    if ($request->hasFile('file_ttd_pembuat')) {
                        $path = $request->file('file_ttd_pembuat')->store('signatures', 'public');
                        $ttd->tanda_tangan = $path;
                    } elseif ($request->hasFile("penandatangan.{$index}.file_ttd")) {
                        $path = $request->file("penandatangan.{$index}.file_ttd")->store('signatures', 'public');
                        $ttd->tanda_tangan = $path;
                    } elseif (auth()->user()->signature) {
                        // Otomatis gunakan tanda tangan dari profil jika user tidak upload file baru
                        $ttd->tanda_tangan = 'signatures/' . auth()->user()->signature;
                    }
                } else {
                    $ttd->status = 'menunggu';
                }
                $ttd->save();
            }
        } elseif ($surat->penandatangan_nama) {
            $surat->penandatangans()->create([
                'user_id' => auth()->id(),
                'peran' => 'pimpinan',
                'urutan' => 1,
                'jabatan' => $surat->penandatangan_jabatan,
                'nama' => $surat->penandatangan_nama,
                'nip' => $surat->penandatangan_nip,
                'status' => 'menunggu'
            ]);
        }

        return redirect()->route('pegawai.surat.index')->with('success', 'Draft surat berhasil dibuat!');
    }

    public function preview($id)
    {
        $surat = Surat::with(['penandatangans.user', 'user', 'pengirim'])->findOrFail($id);
        return view('pegawai.surat.preview_surat', compact('surat'));
    }

    public function edit($id)
    {
        $surat = Surat::with('penandatangans')->findOrFail($id);
        $pimpinanUsers = User::where('role', 'pimpinan')->get();
        return view('pegawai.surat.edit', compact('surat', 'pimpinanUsers'));
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
            'nomor_surat' => 'nullable|string|max:255',
            'sifat' => 'required|string|max:100',
            'lampiran' => 'required|string|max:100',
            'tujuan' => 'required|string',
            'penandatangan' => 'nullable|array',
            'penandatangan.*.id' => 'nullable|integer',
            'penandatangan.*.user_id' => 'nullable|integer',
            'penandatangan.*.peran' => 'nullable|string|max:50',
            'penandatangan.*.urutan' => 'nullable|integer',
            'penandatangan.*.jabatan' => 'nullable|string|max:255',
            'penandatangan.*.nama' => 'nullable|string|max:255',
            'penandatangan.*.nip' => 'nullable|string|max:255',
            'penandatangan.*.file_ttd' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'file_ttd_pembuat' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'penandatangan_jabatan' => 'nullable|string|max:255',
            'penandatangan_nama' => 'nullable|string|max:255',
            'penandatangan_nip' => 'nullable|string|max:255',
            
            'nama_instansi' => 'nullable|string',
            'alamat_instansi' => 'nullable|string',
            'kontak_instansi' => 'nullable|string',
            'email_instansi' => 'nullable|string|max:255',
            'website_instansi' => 'nullable|string|max:255',
            'logo_instansi_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $surat->jenis = $validatedData['jenis'];
        $surat->judul = $validatedData['judul'];
        $surat->isi = Purifier::clean($validatedData['isi']);
        if ($request->filled('nomor_surat')) {
            $surat->nomor_surat = $validatedData['nomor_surat'];
        }
        $surat->sifat = $validatedData['sifat'];
        $surat->lampiran = $validatedData['lampiran'];
        $surat->tujuan = $validatedData['tujuan'];
        $surat->status = 'draft'; // Reset status menjadi 'draft' setiap kali diupdate

        if ($jenis == 'keluar_full') {
            $surat->nama_instansi = Purifier::clean($request->nama_instansi);
            $surat->alamat_instansi = Purifier::clean($request->alamat_instansi);
            
            $telepon = Purifier::clean($request->kontak_instansi ?? '');
            $email = Purifier::clean($request->email_instansi ?? '');
            $website = Purifier::clean($request->website_instansi ?? '');

            if (\Illuminate\Support\Facades\Schema::hasColumn('surats', 'email_instansi') && \Illuminate\Support\Facades\Schema::hasColumn('surats', 'website_instansi')) {
                $surat->kontak_instansi = $telepon;
                $surat->email_instansi = $email;
                $surat->website_instansi = $website;
            } else {
                if (!empty($email) || !empty($website)) {
                    $surat->kontak_instansi = json_encode([
                        'telepon' => $telepon,
                        'email' => $email,
                        'website' => $website,
                    ]);
                } else {
                    $surat->kontak_instansi = $telepon;
                }
            }
            
            // Handle file upload untuk logo jika ada
            if ($request->hasFile('logo_instansi_file')) {
                $path = $request->file('logo_instansi_file')->store('logos', 'public');
                $surat->logo_instansi = $path;
            }

        } else {
            $surat->nama_instansi = null;
            $surat->alamat_instansi = null;
            $surat->kontak_instansi = null;
            if (\Illuminate\Support\Facades\Schema::hasColumn('surats', 'email_instansi')) {
                $surat->email_instansi = null;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('surats', 'website_instansi')) {
                $surat->website_instansi = null;
            }
            $surat->logo_instansi = null;
        }

        // Sinkronisasi data penandatangan
        $penandatanganList = $request->input('penandatangan', []);
        if (!empty($penandatanganList)) {
            $primary = end($penandatanganList);
            $surat->penandatangan_jabatan = $primary['jabatan'] ?? ($request->penandatangan_jabatan ?? '-');
            $surat->penandatangan_nama = $primary['nama'] ?? ($request->penandatangan_nama ?? '-');
            $surat->penandatangan_nip = $primary['nip'] ?? ($request->penandatangan_nip ?? '-');
        } else {
            $surat->penandatangan_jabatan = $request->penandatangan_jabatan;
            $surat->penandatangan_nama = $request->penandatangan_nama;
            $surat->penandatangan_nip = $request->penandatangan_nip;
        }

        $surat->save();

        if (!empty($penandatanganList)) {
            $existingTtds = $surat->penandatangans->keyBy('id');
            $submittedIds = array_filter(array_column($penandatanganList, 'id'));

            // Hapus tanda tangan yang dibuang dari form
            foreach ($existingTtds as $oldId => $oldTtd) {
                if (!in_array($oldId, $submittedIds)) {
                    if ($oldTtd->tanda_tangan) {
                        Storage::disk('public')->delete($oldTtd->tanda_tangan);
                    }
                    $oldTtd->delete();
                }
            }

            foreach ($penandatanganList as $index => $item) {
                if (empty($item['nama']) && empty($item['jabatan'])) continue;
                $ttdId = $item['id'] ?? null;
                $ttd = ($ttdId && isset($existingTtds[$ttdId])) ? $existingTtds[$ttdId] : new SuratPenandatangan();

                $peran = ($item['peran'] ?? 'pimpinan') === 'pembuat' ? 'pembuat' : 'pimpinan';
                $ttd->surat_id = $surat->id;
                $ttd->user_id = !empty($item['user_id']) ? $item['user_id'] : ($peran === 'pembuat' ? auth()->id() : null);
                $ttd->peran = $peran;
                $ttd->urutan = $item['urutan'] ?? ($index + 1);
                $ttd->jabatan = $item['jabatan'] ?? null;
                $ttd->nama = $item['nama'] ?? '';
                $ttd->nip = $item['nip'] ?? null;

                if ($peran === 'pembuat') {
                    $ttd->user_id = auth()->id();
                    $ttd->status = 'ditandatangani';
                    if (!$ttd->signed_at) {
                        $ttd->signed_at = now();
                    }
                    if ($request->hasFile('file_ttd_pembuat')) {
                        if ($ttd->tanda_tangan && !str_contains($ttd->tanda_tangan, auth()->user()->signature ?? '---')) {
                            Storage::disk('public')->delete($ttd->tanda_tangan);
                        }
                        $path = $request->file('file_ttd_pembuat')->store('signatures', 'public');
                        $ttd->tanda_tangan = $path;
                    } elseif ($request->hasFile("penandatangan.{$index}.file_ttd")) {
                        if ($ttd->tanda_tangan && !str_contains($ttd->tanda_tangan, auth()->user()->signature ?? '---')) {
                            Storage::disk('public')->delete($ttd->tanda_tangan);
                        }
                        $path = $request->file("penandatangan.{$index}.file_ttd")->store('signatures', 'public');
                        $ttd->tanda_tangan = $path;
                    } elseif (!$ttd->tanda_tangan && auth()->user()->signature) {
                        $ttd->tanda_tangan = 'signatures/' . auth()->user()->signature;
                    }
                } else {
                    if (!$ttd->exists) {
                        $ttd->status = 'menunggu';
                    }
                }
                $ttd->save();
            }
        }

        return redirect()->route('pegawai.surat.preview', $surat->id)->with('success', 'Surat berhasil diperbarui!');
    }

    public function download($id)
    {
        $surat = Surat::with('penandatangans')->findOrFail($id);
        
        $safeFileName = str_replace('/', '-', $surat->nomor_surat);
        
        $pdf = \PDF::loadView('pegawai.surat.pdf_surat', compact('surat'))
                     ->setPaper('A4', 'portrait');
        
        return $pdf->download('surat_' . $safeFileName . '.pdf');
    }
    
    public function statusSurat(Request $request)
    {
        $userId = auth()->id();

        $query = Surat::where('user_id', $userId)
                    ->where('status', '!=', 'draft')
                    ->with(['penandatangans.user', 'user', 'pengirim']);

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

        $surats = Surat::with('penandatangans')
            ->whereIn('id', $request->ids)
            ->where('user_id', auth()->id())
            ->where('status', 'draft')
            ->get();

        foreach ($surats as $surat) {
            // Urutan penandatangan pimpinan yang belum ditandatangani
            $firstPendingPimpinan = $surat->penandatangans
                ->where('peran', '!=', 'pembuat')
                ->where('status', 'menunggu')
                ->sortBy('urutan')
                ->first();

            $surat->status = 'menunggu';
            if ($firstPendingPimpinan) {
                if (!$firstPendingPimpinan->user_id && $firstPendingPimpinan->jabatan) {
                    $targetJab = strtolower($firstPendingPimpinan->jabatan);
                    $matchedUser = User::where('role', 'pimpinan')
                        ->get()
                        ->first(function($u) use ($targetJab) {
                            $uPos = strtolower($u->position ?? '');
                            $uBag = strtolower($u->bagian ?? '');
                            return ($uPos && (str_contains($targetJab, $uPos) || str_contains($uPos, $targetJab))) ||
                                   ($uBag && str_contains($targetJab, $uBag));
                        });
                    if ($matchedUser) {
                        $firstPendingPimpinan->user_id = $matchedUser->id;
                        $firstPendingPimpinan->save();
                    }
                }
                $surat->disposisi_user_id = $firstPendingPimpinan->user_id;
            } else {
                // Jika tidak ada tanda tangan pimpinan lain (misal hanya tanda tangan sendiri):
                $surat->perlu_disposisi_admin = true;
                $surat->disposisi_user_id = null;
            }
            $surat->save();
        }

        return redirect()->route('pegawai.surat.status')->with('success', 'Surat berhasil diajukan untuk persetujuan.');
    }

    public function destroy($id)
    {
        $surat = Surat::findOrFail($id);

        if ($surat->user_id != auth()->id() && $surat->disposisi_user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus surat ini.');
        }
        if ($surat->status == 'menunggu') {
            return redirect()->back()->with('error', 'Surat yang sedang dalam proses persetujuan tidak dapat dihapus.');
        }
        if ($surat->logo_instansi) {
            Storage::disk('public')->delete($surat->logo_instansi);
        }
        if ($surat->file_path) {
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
    
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:surats,id',
        ]);
    
        $suratIds = $request->input('ids');
        $userId = auth()->id();
    
        // hanya ambil surat milik user atau disposisi user dan status bukan 'menunggu'
        $suratsToDelete = Surat::where(function($q) use ($userId) {
                                   $q->where('user_id', $userId)
                                     ->orWhere('disposisi_user_id', $userId);
                               })
                               ->whereIn('id', $suratIds)
                               ->where('status', '!=', 'menunggu')
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
            foreach ($surat->penandatangans as $ttd) {
                if ($ttd->tanda_tangan && !str_contains($ttd->tanda_tangan, '_signature.')) {
                    Storage::disk('public')->delete($ttd->tanda_tangan);
                }
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

        $inputJenis = $request->input('jenis');
        $jenis = in_array($inputJenis, ['masuk', 'keluar']) ? $inputJenis : 'masuk';

        $surat = new Surat();
        $surat->user_id = auth()->id();
        $surat->jenis = $jenis;
        $surat->judul = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $surat->isi = 'File PDF diunggah.';
        $surat->file_path = $filePath;
        $surat->status = $jenis === 'masuk' ? 'disetujui' : 'menunggu';
        $surat->save();

        $redirectParams = $request->filled('jenis') ? ['jenis' => $request->input('jenis')] : [];
        return redirect()->route('pegawai.surat.index', $redirectParams)->with('success', 'File surat PDF berhasil diunggah.');
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

