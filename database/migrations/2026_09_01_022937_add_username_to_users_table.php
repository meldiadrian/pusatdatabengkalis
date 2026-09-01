<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // username: unik, nullable sementara agar tidak konflik dengan data lama
            $table->string('username')->nullable()->unique()->after('name');
        });

        // Generate username otomatis untuk user lama yang sudah ada di DB
        \App\Models\User::whereNull('username')->orWhere('username', '')->get()
            ->each(function ($user) {
                $base     = Str::slug(explode(' ', trim($user->name))[0], '');
                $base     = preg_replace('/[^a-z0-9]/', '', strtolower($base)) ?: 'user';
                $username = $base;
                $counter  = 1;

                // Pastikan username unik
                while (\App\Models\User::where('username', $username)->where('id', '!=', $user->id)->exists()) {
                    $username = $base . $counter;
                    $counter++;
                }

                $user->update(['username' => $username]);
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
