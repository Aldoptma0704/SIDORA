<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Models\SuratPenandatangan;
use Mews\Purifier\Facades\Purifier;

class SuratController extends Controller
{
    /**
     * Helper: ambil daftar pimpinan yang bisa jadi tujuan disposisi berikutnya
     * (Kabid, Sekretariat, Kadis) — bukan pegawai seksi biasa
     */
    private function getPimpinanDisposisiList($excludeUserId = null)
    {
        $query = User::where('role', 'pimpinan');
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        return $query->get()->groupBy(function ($u) {
            $bagian = strtolower($u->bagian ?? '');
            $pos    = strtolower($u->position ?? '');

            if (str_contains($bagian, 'kepala dinas') || str_contains($pos, 'kepala dinas') || str_contains($pos, 'kadis')) {
                return 'Kepala Dinas';
            }
            if (str_contains($bagian, 'sekretariat') || str_contains($pos, 'sekretaris')) {
                return 'Kepala Sekretariat';
            }
            if (str_contains($bagian, 'uptd') || str_contains($pos, 'kepala uptd')) {
                return 'Kepala UPTD';
            }
            return 'Kepala Bidang / Kepala Staf';
        });
    }

    public function index()
    {
        if (auth()->user()->role !== 'pimpinan') {
            abort(403, 'Akses hanya untuk pimpinan.');
        }

        $userId = auth()->id();

        // Ambil surat dengan status 'menunggu' yang giliran persetujuannya ditujukan kepada pimpinan ini
        $surats = Surat::whereIn('jenis', ['keluar', 'keluar_full'])
            ->where('status', 'menunggu')
            ->where(function ($query) use ($userId) {
                $query->where('disposisi_user_id', $userId)
                      ->orWhere(function ($sub) use ($userId) {
                          // Fallback jika disposisi_user_id null (misal surat draft lama), cek apakah pimpinan ini penandatangan pending
                          $sub->whereNull('disposisi_user_id')
                              ->whereHas('penandatangans', function ($pt) use ($userId) {
                                  $pt->where('user_id', $userId)->where('status', 'menunggu');
                              });
                      });
            })
            ->with(['penandatangans', 'user'])
            ->orderByDesc('created_at')
            ->get();

        $pimpinanDisposisi = $this->getPimpinanDisposisiList(auth()->id());

        $countMenunggu = $surats->count();
        $countDisetujui = SuratPenandatangan::where('user_id', $userId)->where('status', 'disetujui')->count();
        if ($countDisetujui === 0) {
            $countDisetujui = Surat::where('status', 'disetujui')->count();
        }
        $countDisposisi = Surat::where('disposisi_user_id', $userId)->count();
        $countSuratMasuk = Surat::where('jenis', 'masuk')->count();

        return view('pimpinan.surat.index', compact(
            'surats', 
            'pimpinanDisposisi',
            'countMenunggu',
            'countDisetujui',
            'countDisposisi',
            'countSuratMasuk'
        ));
    }

    public function setujui(Request $request, $id)
    {
        $request->validate([
            'disposisi' => 'nullable|string|max:255',
        ]);

        $surat = Surat::with('penandatangans')->findOrFail($id);
        $user = auth()->user();

        // Validasi: hanya pimpinan yang sedang dituju yang dapat menyetujui
        $isMyTurn = ($surat->disposisi_user_id == $user->id) ||
                    ($surat->penandatangans->where('user_id', $user->id)->where('status', 'menunggu')->isNotEmpty());

        if (!$isMyTurn && $surat->disposisi_user_id !== null) {
            abort(403, 'Akses ditolak. Surat ini sedang tidak menunggu persetujuan dari Anda.');
        }

        // 1. Update SuratPenandatangan pimpinan saat ini
        $myTtd = $surat->penandatangans
            ->where('user_id', $user->id)
            ->where('status', 'menunggu')
            ->first();

        // Fallback jika user_id belum terisi di ttd (cocokkan berdasarkan nama/jabatan/bagian)
        if (!$myTtd) {
            $myBagian = strtolower($user->bagian ?? '');
            $myPos    = strtolower($user->position ?? '');

            $myTtd = $surat->penandatangans
                ->where('peran', '!=', 'pembuat')
                ->where('status', 'menunggu')
                ->filter(function($ttd) use ($myBagian, $myPos) {
                    $ttdJab = strtolower($ttd->jabatan ?? '');
                    return $ttdJab && (
                        ($myPos && (str_contains($ttdJab, $myPos) || str_contains($myPos, $ttdJab))) ||
                        ($myBagian && str_contains($ttdJab, $myBagian))
                    );
                })
                ->sortBy('urutan')
                ->first();
        }

        if (!$myTtd) {
            abort(403, 'Akses ditolak. Anda tidak terdaftar sebagai penandatangan surat ini.');
        }

        $myTtd->user_id = $user->id;
        $myTtd->status = 'ditandatangani';
        $myTtd->signed_at = now();
        if ($user->signature) {
            $myTtd->tanda_tangan = 'signatures/' . $user->signature;
        }
        if (!$myTtd->nama && $user->name) {
            $myTtd->nama = $user->name;
        }
        if (!$myTtd->nip && $user->nip) {
            $myTtd->nip = $user->nip;
        }
        $myTtd->save();

        // Simpan catatan disposisi jika ada (opsional)
        if ($request->disposisi) {
            $surat->disposisi = $request->disposisi;
        }
        $surat->pengirim_id = $user->id;

        // 2. Cek apakah masih ada penandatangan pimpinan berikutnya dalam rantai
        $nextTtd = $surat->penandatangans()
            ->where('peran', '!=', 'pembuat')
            ->where('status', 'menunggu')
            ->orderBy('urutan')
            ->first();

        if ($nextTtd) {
            // Jika user_id pada penandatangan berikutnya belum ada, cari akun pimpinan yang cocok
            if (!$nextTtd->user_id && $nextTtd->jabatan) {
                $targetJab = strtolower($nextTtd->jabatan);
                $matchedUser = User::where('role', 'pimpinan')
                    ->get()
                    ->first(function($u) use ($targetJab) {
                        $uPos = strtolower($u->position ?? '');
                        $uBag = strtolower($u->bagian ?? '');
                        return ($uPos && (str_contains($targetJab, $uPos) || str_contains($uPos, $targetJab))) ||
                               ($uBag && str_contains($targetJab, $uBag));
                    });
                if ($matchedUser) {
                    $nextTtd->user_id = $matchedUser->id;
                    $nextTtd->save();
                }
            }

            // Diteruskan ke pejabat penandatangan berikutnya
            $surat->disposisi_user_id = $nextTtd->user_id;
            $surat->status = 'menunggu';
            $surat->save();

            $penerima = $nextTtd->nama ?: $nextTtd->jabatan;
            return redirect()->route('pimpinan.dashboard')->with('success', "Surat berhasil disetujui dan diteruskan ke tahap penandatanganan berikutnya ({$penerima}).");
        } else {
            // Semua penandatangan pimpinan dalam rantai telah menyetujui -> Langsung ke Admin!
            $surat->status = 'disetujui';
            $surat->signed_at = now();
            $surat->perlu_disposisi_admin = true;
            $surat->disposisi_user_id = null;
            $surat->save();

            return redirect()->route('pimpinan.dashboard')->with('success', 'Surat berhasil disetujui sepenuhnya dan telah diteruskan ke Admin.');
        }
    }

    public function disposisi(Request $request)
    {
        if (auth()->user()->role !== 'pimpinan') {
            abort(403, 'Akses hanya untuk pimpinan.');
        }

        $userId = auth()->id();
        $filter = $request->query('status', 'all');

        // Pimpinan hanya melihat surat yang pernah didisposisikan ke mereka, ditandatangani oleh mereka, atau ditolak oleh mereka
        $query = Surat::with(['penandatangans', 'user', 'pengirim'])
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->where(function ($q) use ($userId) {
                $q->where('pengirim_id', $userId)
                  ->orWhere('disposisi_user_id', $userId)
                  ->orWhereHas('penandatangans', function ($pt) use ($userId) {
                      $pt->where('user_id', $userId);
                  });
            });

        if ($filter === 'disetujui') {
            $query->where('status', 'disetujui');
        } elseif ($filter === 'ditolak') {
            $query->where('status', 'ditolak');
        }

        $surats = $query->orderByDesc('updated_at')->get();

        $baseCountQuery = Surat::whereIn('status', ['disetujui', 'ditolak'])
            ->where(function ($q) use ($userId) {
                $q->where('pengirim_id', $userId)
                  ->orWhere('disposisi_user_id', $userId)
                  ->orWhereHas('penandatangans', function ($pt) use ($userId) {
                      $pt->where('user_id', $userId);
                  });
            });

        $totalCount = (clone $baseCountQuery)->count();
        $disetujuiCount = (clone $baseCountQuery)->where('status', 'disetujui')->count();
        $ditolakCount = (clone $baseCountQuery)->where('status', 'ditolak')->count();

        return view('pimpinan.surat.disposisi', compact(
            'surats',
            'filter',
            'totalCount',
            'disetujuiCount',
            'ditolakCount'
        ));
    }

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:255',
        ]);

        $surat = Surat::with('penandatangans')->findOrFail($id);
        $user = auth()->user();

        // Validasi: hanya pimpinan yang sedang dituju yang dapat menolak surat
        $isMyTurn = ($surat->disposisi_user_id == $user->id) ||
                    ($surat->penandatangans->where('user_id', $user->id)->where('status', 'menunggu')->isNotEmpty());

        if (!$isMyTurn && $surat->disposisi_user_id !== null) {
            abort(403, 'Akses ditolak. Surat ini sedang tidak menunggu tindakan dari Anda.');
        }

        // Tandai ttd pimpinan saat ini sebagai ditolak
        $myTtd = $surat->penandatangans
            ->where('user_id', $user->id)
            ->where('status', 'menunggu')
            ->first();

        if (!$myTtd) {
            $myBagian = strtolower($user->bagian ?? '');
            $myPos    = strtolower($user->position ?? '');

            $myTtd = $surat->penandatangans
                ->where('peran', '!=', 'pembuat')
                ->where('status', 'menunggu')
                ->filter(function($ttd) use ($myBagian, $myPos) {
                    $ttdJab = strtolower($ttd->jabatan ?? '');
                    return $ttdJab && (
                        ($myPos && (str_contains($ttdJab, $myPos) || str_contains($myPos, $ttdJab))) ||
                        ($myBagian && str_contains($ttdJab, $myBagian))
                    );
                })
                ->sortBy('urutan')
                ->first();
        }

        if ($myTtd) {
            $myTtd->user_id = $user->id;
            $myTtd->status = 'ditolak';
            $myTtd->catatan = $request->alasan_penolakan;
            $myTtd->save();
        }

        $surat->status = 'ditolak';
        $surat->alasan_penolakan = $request->alasan_penolakan;
        $surat->pengirim_id = $user->id;
        $surat->disposisi_user_id = null;
        $surat->save();

        return redirect()->back()->with('success', 'Surat berhasil ditolak dengan catatan alasan.');
    }

    public function preview($id)
    {
        if (auth()->user()->role !== 'pimpinan') {
            abort(403, 'Akses ditolak.');
        }

        $userId = auth()->id();
        $surat = Surat::with(['penandatangans.user', 'user', 'pengirim'])->findOrFail($id);

        // Pastikan hanya pimpinan yang berhak yang bisa mengakses surat ini:
        // 1. Pembuat surat ini sendiri (user_id)
        // 2. Sedang ditujukan ke pimpinan ini (disposisi_user_id)
        // 3. ATAU pimpinan ini ada dalam daftar penandatangan surat
        // 4. ATAU pimpinan ini yang pernah mendisposisikan/menolak/menyetujui (pengirim_id)
        $hasAccess = ($surat->user_id == $userId) ||
                     ($surat->disposisi_user_id == $userId) ||
                     ($surat->pengirim_id == $userId) ||
                     ($surat->penandatangans->where('user_id', $userId)->isNotEmpty());

        if (!$hasAccess && $surat->status === 'menunggu') {
            $myBagian = strtolower(auth()->user()->bagian ?? '');
            $myPos    = strtolower(auth()->user()->position ?? '');
            $hasAccess = $surat->penandatangans
                ->where('status', 'menunggu')
                ->contains(function($ttd) use ($myBagian, $myPos) {
                    $ttdJab = strtolower($ttd->jabatan ?? '');
                    return $ttdJab && (
                        ($myPos && (str_contains($ttdJab, $myPos) || str_contains($myPos, $ttdJab))) ||
                        ($myBagian && str_contains($ttdJab, $myBagian))
                    );
                });
        }

        if (!$hasAccess) {
            abort(403, 'Akses ditolak. Surat ini tidak ditujukan kepada Anda.');
        }

        $penandatangan = auth()->user();
        $pimpinanDisposisi = $this->getPimpinanDisposisiList(auth()->id());

        return view('pimpinan.surat.preview', compact('surat', 'penandatangan', 'pimpinanDisposisi'));
    }

    public function balasanSurat()
    {
        if (auth()->user()->isKepalaDinas()) {
            return redirect()->route('pimpinan.dashboard')->with('error', 'Kepala Dinas tidak memiliki akses ke pembuatan surat balasan.');
        }

        $pimpinanUsers = User::where('role', 'pimpinan')->get();
        return view('pimpinan.surat.suratbalasan', compact('pimpinanUsers'));
    }

    public function simpanSuratBalasan(Request $request)
    {
        if (auth()->user()->isKepalaDinas()) {
            abort(403, 'Kepala Dinas tidak dapat membuat surat balasan.');
        }

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
        $surat->pengirim_id = auth()->id();
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

        $penandatanganList = $request->input('penandatangan', []);
        if (!empty($penandatanganList)) {
            $primary = end($penandatanganList);
            $surat->penandatangan_jabatan = $primary['jabatan'] ?? ($request->penandatangan_jabatan ?? '-');
            $surat->penandatangan_nama = $primary['nama'] ?? ($request->penandatangan_nama ?? '-');
            $surat->penandatangan_nip = $primary['nip'] ?? ($request->penandatangan_nip ?? '-');
        }

        $surat->status = 'draft';
        $surat->save();

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
                        $ttd->tanda_tangan = 'signatures/' . auth()->user()->signature;
                    }
                } else {
                    $ttd->status = 'menunggu';
                }
                $ttd->save();
            }
        }

        return redirect()->route('pimpinan.status-surat')->with('success', 'Draft surat balasan berhasil dibuat!');
    }

    public function statusSuratBalasan(Request $request)
    {
        if (auth()->user()->isKepalaDinas()) {
            return redirect()->route('pimpinan.dashboard')->with('error', 'Kepala Dinas tidak memiliki akses ke menu status surat pembuat.');
        }

        $userId = auth()->id();
        $tab = $request->get('tab', 'semua');
        $search = $request->get('search');

        $baseQuery = Surat::where('user_id', $userId)
            ->whereIn('jenis', ['keluar', 'keluar_full', 'balasan']);

        $jumlahDraft = (clone $baseQuery)->where('status', 'draft')->count();
        $jumlahMenunggu = (clone $baseQuery)->where('status', 'menunggu')->count();
        $jumlahDisetujui = (clone $baseQuery)->where('status', 'disetujui')->count();
        $jumlahDitolak = (clone $baseQuery)->where('status', 'ditolak')->count();
        $totalSurat = (clone $baseQuery)->whereIn('status', ['draft', 'menunggu', 'disetujui', 'ditolak'])->count();

        $query = clone $baseQuery;

        if ($tab === 'draft') {
            $query->where('status', 'draft');
        } elseif ($tab === 'menunggu') {
            $query->where('status', 'menunggu');
        } elseif ($tab === 'disetujui') {
            $query->where('status', 'disetujui');
        } elseif ($tab === 'ditolak') {
            $query->where('status', 'ditolak');
        } else {
            $query->whereIn('status', ['draft', 'menunggu', 'disetujui', 'ditolak']);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%");
            });
        }

        $surats = $query->with('penandatangans')->orderByDesc('updated_at')->get();

        $drafts = Surat::where('user_id', $userId)
            ->where('status', 'draft')
            ->orderByDesc('created_at')
            ->get();

        return view('pimpinan.surat.status_surat', compact(
            'surats',
            'drafts',
            'tab',
            'jumlahDraft',
            'jumlahMenunggu',
            'jumlahDisetujui',
            'jumlahDitolak',
            'totalSurat'
        ));
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
                $surat->perlu_disposisi_admin = true;
                $surat->disposisi_user_id = null;
            }
            $surat->save();
        }

        return redirect()->route('pimpinan.status-surat')->with('success', 'Surat balasan berhasil diajukan untuk persetujuan.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:surats,id',
        ]);

        $suratIds = $request->input('ids');
        $userId = auth()->id();

        $suratsToDelete = Surat::where('user_id', $userId)
            ->whereIn('id', $suratIds)
            ->whereIn('status', ['draft', 'disetujui', 'ditolak'])
            ->get();

        if ($suratsToDelete->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada surat yang valid untuk dihapus.');
        }

        foreach ($suratsToDelete as $surat) {
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
        }

        return redirect()->back()->with('success', 'Surat yang dipilih berhasil dihapus.');
    }

    public function destroy($id)
    {
        $surat = Surat::findOrFail($id);

        if ($surat->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus surat ini.');
        }

        if ($surat->status == 'menunggu') {
            return redirect()->back()->with('error', 'Surat yang sedang dalam proses verifikasi tidak dapat dihapus.');
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

    public function suratMasuk(Request $request)
    {
        if (auth()->user()->role !== 'pimpinan') {
            abort(403, 'Akses ditolak.');
        }

        $userId = auth()->id();
        $search = $request->get('search');

        $query = Surat::where('jenis', 'masuk')
            ->where(function($q) use ($userId) {
                $q->where('disposisi_user_id', $userId)
                  ->orWhere('user_id', $userId);
            });

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('nama_instansi', 'like', "%{$search}%");
            });
        }

        $surats = $query->orderByDesc('created_at')->get();

        return view('pimpinan.surat.surat_masuk', compact('surats'));
    }

    public function destroySuratMasuk($id)
    {
        if (auth()->user()->role !== 'pimpinan') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $userId = auth()->id();
        $surat = Surat::where('jenis', 'masuk')
            ->where(function($q) use ($userId) {
                $q->where('disposisi_user_id', $userId)->orWhere('user_id', $userId);
            })
            ->findOrFail($id);

        if ($surat->file_path) {
            Storage::disk('public')->delete($surat->file_path);
        }

        $surat->delete();

        return redirect()->back()->with('success', 'Surat masuk berhasil dihapus.');
    }

    public function bulkDeleteSuratMasuk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:surats,id',
        ]);

        if (auth()->user()->role !== 'pimpinan') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $userId = auth()->id();

        $suratsToDelete = Surat::where('jenis', 'masuk')
            ->where(function($q) use ($userId) {
                $q->where('disposisi_user_id', $userId)->orWhere('user_id', $userId);
            })
            ->whereIn('id', $request->input('ids'))
            ->get();

        if ($suratsToDelete->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada surat yang valid untuk dihapus.');
        }

        $count = $suratsToDelete->count();
        foreach ($suratsToDelete as $surat) {
            if ($surat->file_path) {
                Storage::disk('public')->delete($surat->file_path);
            }
            $surat->delete();
        }

        return redirect()->back()->with('success', $count . ' surat masuk berhasil dihapus.');
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
        $surat->status = 'disetujui';
        $surat->file_path = $filePath;
        $surat->tujuan = $request->input('tujuan') ?: '-';
        $surat->nama_instansi = $request->input('nama_instansi') ?: '-';
        $surat->save();

        return redirect()->back()->with('success', 'File PDF Surat Masuk berhasil diunggah!');
    }

    public function edit(Surat $surat)
    {
        if ($surat->user_id != auth()->id()) {
            abort(403, 'Akses ditolak.');
        }
        $pimpinanUsers = User::where('role', 'pimpinan')->get();
        return view('pimpinan.surat.editbalasan', compact('surat', 'pimpinanUsers'));
    }

    public function update(Request $request, Surat $surat)
    {
        if ($surat->user_id != auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'tujuan' => 'nullable|string|max:255',
            'isi' => 'required|string',
        ]);

        $surat->update($validatedData);

        return redirect()->route('pimpinan.status-surat')->with('success', 'Surat balasan berhasil diperbarui!');
    }

    public function download(Surat $surat)
    {
        // Pastikan pengguna yang terautentikasi saat ini memiliki peran 'pimpinan' untuk mengunduh
        if (Auth::user()->role !== 'pimpinan') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengunduh surat ini.');
        }

        // Jika surat merupakan file PDF yang diunggah
        if ($surat->file_path && Storage::disk('public')->exists($surat->file_path)) {
            return Storage::disk('public')->download($surat->file_path, 'surat_' . Str::slug($surat->judul) . '.pdf');
        }

        // Jika surat memiliki struktur penandatangan atau surat keluar resmi
        if ($surat->penandatangans()->exists() || in_array($surat->jenis, ['keluar', 'keluar_full'])) {
            $surat->load(['penandatangans', 'user', 'pengirim']);
            $safeFileName = str_replace('/', '-', $surat->nomor_surat ?: Str::slug($surat->judul));
            $pdf = Pdf::loadView('pegawai.surat.pdf_surat', compact('surat'))
                      ->setPaper('A4', 'portrait');
            return $pdf->download('surat_' . $safeFileName . '.pdf');
        }

        $user = Auth::user();

        // Base64 encode logo utama
        $base64Logo = '';
        $logoPath = public_path('img/logo_lampung.png');

        if (file_exists($logoPath)) {
            $base64Logo = base64_encode(file_get_contents($logoPath));
        }

        $base64Signature = '';
        if ($user && $user->signature) {
            $signaturePath = Storage::disk('public')->path('signatures/' . $user->signature);
            if (file_exists($signaturePath)) {
                $type = pathinfo($signaturePath, PATHINFO_EXTENSION);
                $base64Signature = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($signaturePath));
            }
        }

        // Base64 encode logo instansi jika ada dan jenis surat adalah 'keluar_full'
        $base64InstansiLogo = '';
        if ($surat->jenis === 'keluar_full' && $surat->logo_instansi) {
            $instansiLogoPath = Storage::disk('public')->path($surat->logo_instansi); 
            if (file_exists($instansiLogoPath)) {
                $type = pathinfo($instansiLogoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($instansiLogoPath);
                $base64InstansiLogo = 'data:image/' . $type . ';base64,' . base64_encode($data);
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
