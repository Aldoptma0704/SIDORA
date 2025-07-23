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
        return view('admin.dashboard', compact('users'));
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

        return redirect()->route('admin.dashboard')->with('success', 'Pengguna berhasil ditambahkan.');
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

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diupdate.');
    }

    // Hapus Pengguna
    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    public function laporan()
    {
        $surats = Surat::with('user')->latest()->get();
        return view('admin.laporan.index', compact('surats'));
    }

    //surat masuk dari pimpinan
    // public function suratMasuk()
    // {
    //     $surats = \App\Models\Surat::where('status_balasan', 'dikirim')
    //                 ->latest()
    //                 ->get();

    //     return view('admin.surat.masuk', compact('surats'));
    // }

    // public function lihat($id)
    // {
    //     $surat = \App\Models\Surat::findOrFail($id);
    //     return view('admin.surat.lihat', compact('surat'));
    // }

}
