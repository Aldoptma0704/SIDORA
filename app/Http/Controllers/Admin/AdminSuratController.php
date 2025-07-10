<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;

class AdminSuratController extends Controller
{
    public function disposisiMasuk()
    {
        $surats = Surat::where('perlu_disposisi_admin', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.surat.disposisi_masuk', compact('surats'));
    }
}
