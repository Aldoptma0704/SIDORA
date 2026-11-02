<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pengirim_id',
        'judul', 
        'jenis', 
        'isi',
        'status',
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
        'file_path',
        'signed_at',
        'alasan_penolakan',
        'logo_instansi',
        'nama_instansi',
        'alamat_instansi',
        'kontak_instansi',
        'email_instansi',
        'website_instansi',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public function penandatangans()
    {
        return $this->hasMany(\App\Models\SuratPenandatangan::class)->orderBy('urutan');
    }

    /**
     * Accessor untuk nama instansi yang bersih dan rapi untuk kop surat
     */
    public function getNamaInstansiFormattedAttribute()
    {
        $raw = $this->nama_instansi;
        if (empty($raw)) {
            return '';
        }
        // Bersihkan tag kosong <p><br></p> atau &nbsp;
        $clean = preg_replace('/<p[^>]*>(\s|&nbsp;|<br\s*\/?>)*<\/p>/i', '', $raw);
        $clean = trim($clean);
        return $clean;
    }

    /**
     * Accessor untuk nomor telepon / kontak instansi bersih
     */
    public function getTeleponInstansiAttribute()
    {
        $val = $this->kontak_instansi;
        if ($val && str_starts_with(trim($val), '{')) {
            $json = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($json['telepon'])) {
                return trim(strip_tags($json['telepon']));
            }
        }
        return trim(strip_tags($val ?? ''));
    }

    /**
     * Accessor untuk email instansi
     */
    public function getEmailInstansiAttribute($value = null)
    {
        if (!empty($value)) {
            return trim(strip_tags($value));
        }
        $val = $this->attributes['kontak_instansi'] ?? null;
        if ($val && str_starts_with(trim($val), '{')) {
            $json = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($json['email'])) {
                return trim(strip_tags($json['email']));
            }
        }
        return null;
    }

    /**
     * Accessor untuk website instansi
     */
    public function getWebsiteInstansiAttribute($value = null)
    {
        if (!empty($value)) {
            return trim(strip_tags($value));
        }
        $val = $this->attributes['kontak_instansi'] ?? null;
        if ($val && str_starts_with(trim($val), '{')) {
            $json = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && !empty($json['website'])) {
                return trim(strip_tags($json['website']));
            }
        }
        return null;
    }

    /**
     * Accessor untuk alamat instansi bersih
     */
    public function getAlamatInstansiCleanAttribute()
    {
        return trim(strip_tags($this->alamat_instansi ?? ''));
    }
}
