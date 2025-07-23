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
            $table->boolean('is_balasan')->default(false); // apakah ini surat balasan
            $table->string('status_balasan')->nullable(); // draft, dikirim, dll
            $table->unsignedBigInteger('surat_asal_id')->nullable(); // ID surat yang dibalas (optional)

            // Jika kamu ingin relasi foreign key
            $table->foreign('surat_asal_id')->references('id')->on('surats')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropForeign(['surat_asal_id']);
            $table->dropColumn(['is_balasan', 'status_balasan', 'surat_asal_id']);
        });
    }
};
