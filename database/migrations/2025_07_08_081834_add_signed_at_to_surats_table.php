<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Menambahkan kolom signed_at ke tabel surats.
     */
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->timestamp('signed_at')->nullable()->after('status');
        });
    }

    /**
     * Menghapus kolom signed_at jika migrasi dibatalkan.
     */
    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropColumn('signed_at');
        });
    }
};
