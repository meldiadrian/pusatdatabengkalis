<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahBengkalisSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Kecamatan Bengkalis
        $bengkalis = Kecamatan::firstOrCreate([
            'nama_kecamatan' => 'Bengkalis',
        ]);

        // Daftar desa di Kecamatan Bengkalis
        $desas = [
            'Air Putih',
            'Bengkalis Kota',
            'Damon',
            'Kelebuk',
            'Kelapapati',
            'Pedekik',
            'Penampi',
            'Penebal',
            'Rimba Sekampung',
            'Sebauk',
            'Sekodi',
            'Senggoro',
            'Teluk Latak',
            'Wonosari',
        ];

        // 🔥 URUTKAN A–Z
        sort($desas);

        // Insert semua desa
        foreach ($desas as $desa) {
            Desa::firstOrCreate([
                'kecamatan_id' => $bengkalis->id,
                'nama_desa' => $desa,
            ]);
        }
    }
}
