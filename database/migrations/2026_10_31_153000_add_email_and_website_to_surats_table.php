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
            if (!Schema::hasColumn('surats', 'email_instansi')) {
                $table->string('email_instansi')->nullable()->after('kontak_instansi');
            }
            if (!Schema::hasColumn('surats', 'website_instansi')) {
                $table->string('website_instansi')->nullable()->after('email_instansi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            if (Schema::hasColumn('surats', 'website_instansi')) {
                $table->dropColumn('website_instansi');
            }
            if (Schema::hasColumn('surats', 'email_instansi')) {
                $table->dropColumn('email_instansi');
            }
        });
    }
};
