<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->unsignedBigInteger('pengirim_id')->nullable()->after('isi');
            // $table->unsignedBigInteger('user_id')->nullable()->after('pengirim_id'); // Sudah ada
        });
    }

    public function down()
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropColumn(['pengirim_id', 'user_id']);
        });
    }

};
