<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahBantanSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Kecamatan Bantan
        $bantan = Kecamatan::firstOrCreate([
            'nama_kecamatan' => 'Bantan',
        ]);

        // Daftar desa di Kecamatan Bantan
        $desas = [
            'Bantan Air',
            'Bantan Tengah',
            'Bantan Sari',
            'Bantan Tua',
            'Deluk',
            'Kembung Baru',
            'Muntai',
            'Pambang Baru',
            'Pambang Pesisir',
            'Selat Baru',
            'Teluk Papal',
            'Berancah',
        ];

        // Insert semua desa
        foreach ($desas as $desa) {
            Desa::firstOrCreate([
                'kecamatan_id' => $bantan->id,
                'nama_desa' => $desa,
            ]);
        }
    }
}