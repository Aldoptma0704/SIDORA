<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            // Tambahkan kolom spesifik untuk surat keluar, buat nullable karena tidak semua surat memilikinya.
            $table->string('nomor_surat')->nullable()->after('judul');
            $table->string('sifat')->default('Biasa')->nullable()->after('nomor_surat');
            $table->string('lampiran')->default('-')->nullable()->after('sifat');
            $table->text('tujuan')->nullable()->after('lampiran'); // Pakai text untuk alamat yang panjang
            $table->string('penandatangan_jabatan')->nullable()->after('isi');
            $table->string('penandatangan_nama')->nullable()->after('penandatangan_jabatan');
            $table->text('penandatangan_nip')->nullable()->after('penandatangan_nama'); // Pakai text jika NIP/pangkat panjang
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_surat',
                'sifat',
                'lampiran',
                'tujuan',
                'penandatangan_jabatan',
                'penandatangan_nama',
                'penandatangan_nip',
            ]);
        });
    }
};
