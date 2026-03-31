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
        Schema::table('daftar_aplikasi_perangkat_daerahs', function (Blueprint $table) {
            $table->string('unitkerja_webopd')->nullable()->after('websiteopd');
            $table->text('pembuat_webopd')->nullable();
            $table->string('status_webopd')->nullable();
            $table->boolean('keterangan_webopd')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_aplikasi_perangkat_daerahs', function (Blueprint $table) {
            //
        });
    }
};
