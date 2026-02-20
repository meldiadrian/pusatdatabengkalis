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
        Schema::table('pemohons', function (Blueprint $table) {
        $table->unsignedBigInteger('instansi_pemohon')->change();
        $table->unsignedBigInteger('opd_tujuan')->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemohons', function (Blueprint $table) {
        $table->string('instansi_pemohon')->change();
        $table->string('opd_tujuan')->change();
    });
    }
};
