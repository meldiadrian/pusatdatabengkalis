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
        Schema::table('website_desas', function (Blueprint $table) {
            $table->foreignId('kecamatan_id')
                ->nullable()
                ->constrained('kecamatans')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_desas', function (Blueprint $table) {
            //
        });
    }
};
