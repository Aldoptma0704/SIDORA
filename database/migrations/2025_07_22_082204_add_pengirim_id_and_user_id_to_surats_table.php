<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('surats', function (Blueprint $table) {
            if (!Schema::hasColumn('surats', 'pengirim_id')) {
                $table->unsignedBigInteger('pengirim_id')->nullable()->after('isi');
            }
        });
    }

    public function down()
    {
        Schema::table('surats', function (Blueprint $table) {
            if (Schema::hasColumn('surats', 'pengirim_id')) {
                $table->dropColumn('pengirim_id');
            }

            if (Schema::hasColumn('surats', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
