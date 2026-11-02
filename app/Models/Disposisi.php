<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    use HasFactory;

    protected $table = 'disposis'; 
    protected $fillable = ['surat_id', 'pimpinan_id', 'catatan'];

    public function surat()
    {
        return $this->belongsTo(Surat::class);
    }
}
