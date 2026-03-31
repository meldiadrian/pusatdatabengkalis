<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahBathinSolapanSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Kecamatan Bathin Solapan
        $bathinSolapan = Kecamatan::firstOrCreate([
            'nama_kecamatan' => 'Bathin Solapan',
        ]);

        // Daftar desa di Kecamatan Bathin Solapan
        $desas = [
            'Balai Pungut',
            'Bathin Sobanga',
            'Bumbung',
            'Harapan Baru',
            'Kesumbo Ampai',
            'Pamesi',
            'Petani',
            'Sebangar',
            'Tambusai Batang Dui',
        ];

        // Insert semua desa
        foreach ($desas as $desa) {
            Desa::firstOrCreate([
                'kecamatan_id' => $bathinSolapan->id,
                'nama_desa' => $desa,
            ]);
        }
    }
}
