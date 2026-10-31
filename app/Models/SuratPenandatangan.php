<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPenandatangan extends Model
{
    use HasFactory;

    protected $table = 'surat_penandatangan';

    protected $fillable = [
        'surat_id',
        'user_id',
        'peran',
        'urutan',
        'jabatan',
        'nama',
        'nip',
        'status',
        'tanda_tangan',
        'signed_at',
        'catatan',
    ];

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
