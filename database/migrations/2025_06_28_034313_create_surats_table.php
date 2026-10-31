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
        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pengirim_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('judul');
            $table->string('nomor_surat')->nullable();
            $table->string('sifat')->default('Biasa')->nullable();
            $table->string('lampiran')->default('-')->nullable();
            $table->text('tujuan')->nullable();
            $table->string('jenis'); // e.g. masuk, keluar, keluar_full
            $table->text('isi');
            $table->string('file_path')->nullable();

            // Header instansi (kop surat)
            $table->string('logo_instansi')->nullable();
            $table->string('nama_instansi')->nullable();
            $table->string('alamat_instansi')->nullable();
            $table->string('kontak_instansi')->nullable();

            // Penandatangan
            $table->string('penandatangan_jabatan')->nullable();
            $table->string('penandatangan_nama')->nullable();
            $table->text('penandatangan_nip')->nullable();
            $table->timestamp('signed_at')->nullable();

            // Disposisi & Status
            $table->enum('status', ['draft_pimpinan', 'draft', 'menunggu', 'disetujui', 'ditolak', 'dikirim'])->default('draft');
            $table->text('alasan_penolakan')->nullable();
            $table->string('disposisi')->nullable();
            $table->foreignId('disposisi_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('perlu_disposisi_admin')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surats');
    }
};
