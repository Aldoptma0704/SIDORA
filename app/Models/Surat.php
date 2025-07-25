<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul', // Ini kita gunakan sebagai 'Hal'
        'jenis', // 'masuk' atau 'keluar'
        'isi',
        'status',
        // Tambahkan semua kolom baru di sini
        'nomor_surat',
        'sifat',
        'lampiran',
        'tujuan',
        'penandatangan_jabatan',
        'penandatangan_nama',
        'penandatangan_nip',
        'disposisi',
        'disposisi_user_id',
        'perlu_disposisi_admin',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function disposisiKepada()
    {
        return $this->belongsTo(User::class, 'disposisi_user_id');
    }

    //
    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }
    public function disposisi()
    {
        return $this->hasOne(\App\Models\Disposisi::class);
    }
}
