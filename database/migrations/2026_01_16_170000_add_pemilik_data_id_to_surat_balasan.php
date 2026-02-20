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
        Schema::table('surat_balasan', function (Blueprint $table) {
            $table->foreignId('pemilik_data_id')->nullable()->constrained('pemilik_data')->cascadeOnDelete()->after('pemohon_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_balasan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pemilik_data_id');
        });
    }
};
