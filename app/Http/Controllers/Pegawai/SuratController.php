<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;

class SuratController extends Controller
{
    public function index()
    {
        $surats = Surat::where('user_id', auth()->id())->get();
        return view('pegawai.surat.index', compact('surats'));
    }

    public function create()    
    {
        return view('pegawai.surat.create_surat');
    }

    public function store(Request $request)
    {
                // Validasi dasar
        $validated = $request->validate([
            'jenis' => 'required|in:masuk,keluar',
            'judul' => 'required|string|max:255', // Judul/Hal
            'isi' => 'required|string',
            // 'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg.png'
            'lampiran => required|string|max:100'
        ]);

        // Validasi kondisional untuk surat keluar
        if ($request->jenis == 'keluar') {
            $request->validate([
                'nomor_surat' => 'required|string|max:255',
                'sifat' => 'required|string|max:100',
                'lampiran' => 'required|string|max:100',
                'tujuan' => 'required|string',
                'penandatangan_jabatan' => 'required|string',
                'penandatangan_nama' => 'required|string',
                'penandatangan_nip' => 'required|string',
            ]);
        }

        // $lampiranPath = null;
        // if ($request->hasFile('lampiran')) {
        //     $lampiranPath = $request->file('lampiran')->store('lampiran_surat', 'public');
        // }

        $surat = new Surat();
        $surat->user_id = auth()->id(); // Asumsi ada relasi ke user
        $surat->jenis = $request->jenis;
        $surat->judul = $request->judul; // 'judul' kita gunakan sebagai 'Hal'
        $surat->isi = $request->isi;
        $surat->status = 'menunggu'; // Status default

        if ($request->jenis == 'keluar') {
            $surat->nomor_surat = $request->nomor_surat;
            $surat->sifat = $request->sifat;
            $surat->lampiran = $request->lampiran;
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

        // Menampilkan halaman edit
    public function edit($id)
    {
        $surat = Surat::findOrFail($id);
        return view('pegawai.surat.edit_surat', compact('surat'));
    }

    // Generate PDF
    public function download($id)
    {
        $surat = Surat::findOrFail($id);

        $pdf = \PDF::loadView('pegawai.surat.pdf_surat', compact('surat'))
                   ->setPaper('A4', 'portrait');

        return $pdf->download('surat_' . $surat->nomor_surat . '.pdf');
    }


}
    