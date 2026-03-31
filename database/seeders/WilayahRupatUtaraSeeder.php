<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahRupatUtaraSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Kecamatan Rupat Utara
        $rupatUtara = Kecamatan::firstOrCreate([
            'nama_kecamatan' => 'Rupat Utara',
        ]);

        // Daftar desa di Kecamatan Rupat Utara
        $desas = [
            'Tanjung Medang',
            'Teluk Rhu',
            'Titi Akar',
            'Hutan Ayu',
            'Puteri Sembilan',
            'Kadur',
        ];

        // Insert semua desa
        foreach ($desas as $desa) {
            Desa::firstOrCreate([
                'kecamatan_id' => $rupatUtara->id,
                'nama_desa' => $desa,
            ]);
        }
    }
}
