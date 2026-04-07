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
            $table->enum('tipe', ['OPD', 'Desa'])
                ->default('Desa')
                ->after('unit_kerja_id');
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
