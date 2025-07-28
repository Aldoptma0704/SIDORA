<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;
use Illuminate\Support\Facades\Auth; // Pastikan ini di-import
use Illuminate\Support\Facades\Storage; // Pastikan ini di-import
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Pastikan ini di-import jika digunakan di controller
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan ini di-import jika digunakan untuk download PDF
use Illuminate\Support\Str; // Pastikan ini di-import jika digunakan untuk slug

class SuratController extends Controller
{
    public function suratMasuk()
    {
        $surats = Surat::where('status', 'dikirim')->latest()->get();
        return view('admin.surat.masuk', compact('surats'));
    }

    public function lihat(Request $request, $id)
    {
        $surat = Surat::findOrFail($id);
        // Pastikan hanya admin atau pemilik surat yang bisa melihat
        if (Auth::user()->role !== 'admin' && Auth::user()->id !== $surat->user_id) {
            abort(403, 'Akses ditolak.');
        }

        // Menggunakan pengirim surat sebagai penandatangan untuk tampilan ini
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

        // Base64 encode the institution logo if it exists (for 'keluar_full' type)
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
            'base64Logo' => $base64Logo,
            'base64Signature' => $base64Signature,
            'base64InstansiLogo' => $base64InstansiLogo,
        ];

        // Check if the request is for a PDF-friendly preview
        if ($request->query('is_pdf')) {
            $data['is_pdf'] = true;
        }

        return view('admin.surat.lihat', $data);
    }

    public function disposisiMasuk()
    {
        // Ambil semua surat yang punya relasi ke disposisi
        $surats = Surat::whereHas('disposisi')->latest()->get();

        return view('admin.surat.disposisi_masuk', compact('surats'));
    }

    /**
     * Generate and download the PDF for the specified surat (for Admin).
     *
     * @param  \App\Models\Surat  $surat
     * @return \Illuminate\Http\Response
     */
    public function download(Surat $surat)
    {
        // Pastikan hanya admin yang bisa mengunduh
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengunduh surat ini.');
        }

        // Menggunakan pengirim surat sebagai penandatangan untuk tampilan ini
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

        // Load the dedicated PDF template
        // BARIS YANG ANDA TANYAKAN BERADA DI SINI
        $pdf = Pdf::loadView('admin.surat.pdf_template', $data); 
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]); 

        return $pdf->download('surat_' . Str::slug($surat->judul) . '.pdf');
    }
}
