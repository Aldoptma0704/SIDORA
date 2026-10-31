<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Surat;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Dashboard Admin
    public function dashboard()
    {
        $users = User::all();
        $totalUsers = $users->count();
        $countAdmin = $users->where('role', 'admin')->count();
        $countPegawai = $users->where('role', 'pegawai')->count();
        $countPimpinan = $users->where('role', 'pimpinan')->count();

        $totalSuratMasuk = Surat::where('jenis', 'masuk')->count();
        $totalSuratKeluar = Surat::whereIn('jenis', ['keluar', 'keluar_full'])->count();
        $suratBelumDisposisi = Surat::where('jenis', 'masuk')
            ->where(function($q) {
                $q->whereNull('disposisi_user_id')
                  ->orWhere('status', 'draft');
            })
            ->count();

        $suratMasukTerbaru = Surat::where('jenis', 'masuk')
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'users',
            'totalUsers',
            'countAdmin',
            'countPegawai',
            'countPimpinan',
            'totalSuratMasuk',
            'totalSuratKeluar',
            'suratBelumDisposisi',
            'suratMasukTerbaru'
        ));
    }

    // Daftar Pengguna
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    // Form Tambah
    public function create()
    {
        return view('admin.users.create');
    }

    // Proses Simpan Pengguna Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:admin,pegawai,pimpinan',
            'bagian' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255', 
            'nip' => 'nullable|string|max:50',      
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'bagian' => $request->bagian,
            'position' => $request->position, 
            'nip' => $request->nip,           
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    // Form Edit
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Proses Update Pengguna
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,pegawai,pimpinan',
            'bagian' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255', 
            'nip' => 'nullable|string|max:50',       
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'bagian' => $request->bagian,
            'position' => $request->position, 
            'nip' => $request->nip,           
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diupdate.');
    }

    // Hapus Pengguna
    public function destroy(User $user)
    {
        // Tidak bisa hapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }
        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    // Hapus Banyak Pengguna Sekaligus (Bulk Delete)
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $ids = array_filter($request->ids, fn($id) => (int)$id !== auth()->id());

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada pengguna yang dapat dihapus (Anda tidak dapat menghapus akun sendiri).');
        }

        $count = User::whereIn('id', $ids)->count();
        User::whereIn('id', $ids)->delete();

        return back()->with('success', "{$count} pengguna berhasil dihapus.");
    }

    public function laporan()
    {
        // Surat yang sudah disetujui sepenuhnya (baik dari pegawai maupun pimpinan) masuk ke admin untuk penomoran resmi
        $surats = Surat::where('status', 'disetujui')
          ->whereIn('jenis', ['keluar', 'keluar_full', 'balasan'])
          ->with(['user', 'penandatangans', 'pengirim'])
          ->latest()
          ->get();
        
        $nextNomorUrut = \App\Http\Controllers\Admin\SuratController::getNextNomorUrut(date('Y'));
        $klasifikasiList = \App\Http\Controllers\Admin\SuratController::getKlasifikasiArsip();

        return view('admin.laporan.index', compact('surats', 'nextNomorUrut', 'klasifikasiList'));
    }
}
