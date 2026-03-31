<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahPinggirSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Kecamatan Pinggir
        $pinggir = Kecamatan::firstOrCreate([
            'nama_kecamatan' => 'Pinggir',
        ]);

        // Daftar desa di Kecamatan Pinggir
        $desas = [
            'Balai Raja',
            'Buluh Apo',
            'Muara Basung',
            'Pangkalan Libut',
            'Semunai',
            'Sungai Meranti',
            'Tengganau',
        ];

        // Insert semua desa
        foreach ($desas as $desa) {
            Desa::firstOrCreate([
                'kecamatan_id' => $pinggir->id,
                'nama_desa' => $desa,
            ]);
        }
    }
}
