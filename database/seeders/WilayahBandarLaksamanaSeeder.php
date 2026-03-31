<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahBandarLaksamanaSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Kecamatan Bandar Laksamana
        $bandarLaksamana = Kecamatan::firstOrCreate([
            'nama_kecamatan' => 'Bandar Laksamana',
        ]);

        // Daftar desa di Bandar Laksamana
        $desas = [
            'Api-Api',
            'Bukit Batu',
            'Sepahat',
            'Tanjung Leban',
            'Tenggayun',
            'Parit I Api-Api',
            'Parit II Api-Api',
            'Bukit Kerikil',
        ];

        // Insert semua desa
        foreach ($desas as $desa) {
            Desa::firstOrCreate([
                'kecamatan_id' => $bandarLaksamana->id,
                'nama_desa' => $desa,
            ]);
        }
    }
}
