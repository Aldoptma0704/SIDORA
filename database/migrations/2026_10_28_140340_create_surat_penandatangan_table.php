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
        Schema::create('surat_penandatangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_id')->constrained('surats')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('peran', ['pembuat', 'pimpinan'])->default('pembuat');
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->string('jabatan')->nullable();
            $table->string('nama');
            $table->string('nip')->nullable();
            $table->enum('status', ['menunggu', 'ditandatangani', 'ditolak'])->default('ditandatangani');
            $table->string('tanda_tangan')->nullable(); // Path ke file tanda tangan (untuk pembuat/sendiri)
            $table->timestamp('signed_at')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();
            
            // Indeks untuk efisiensi
            $table->index(['surat_id', 'urutan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_penandatangan');
    }
};
