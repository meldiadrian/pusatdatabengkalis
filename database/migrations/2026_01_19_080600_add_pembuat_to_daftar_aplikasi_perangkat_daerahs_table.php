<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daftar_aplikasi_perangkat_daerahs', function (Blueprint $table) {
            $table->string('pembuat')->nullable()->after('nama_aplikasi');
        });
    }

    public function down(): void
    {
        Schema::table('daftar_aplikasi_perangkat_daerahs', function (Blueprint $table) {
            $table->dropColumn('pembuat');
        });
    }
};
