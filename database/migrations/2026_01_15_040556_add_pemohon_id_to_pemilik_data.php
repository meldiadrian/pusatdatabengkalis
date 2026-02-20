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
        Schema::table('pemilik_data', function (Blueprint $table) {
            //
              if (!Schema::hasColumn('pemilik_data', 'pemohon_id')) {
                    $table->foreignId('pemohon_id')->constrained()->cascadeOnDelete();
    }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemilik_data', function (Blueprint $table) {
            //
        });
    }
};
