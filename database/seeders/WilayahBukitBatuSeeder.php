<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahBukitBatuSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Kecamatan Bukit Batu
        $bukitBatu = Kecamatan::firstOrCreate([
            'nama_kecamatan' => 'Bukit Batu',
        ]);

        // Daftar desa di Kecamatan Bukit Batu
        $desas = [
            'Batang Duku',
            'Buruk Bakul',
            'Dompas',
            'Pakning Asal',
            'Pangkalan Jambi',
            'Sejangat',
            'Sungai Pakning',
        ];

        // Insert semua desa
        foreach ($desas as $desa) {
            Desa::firstOrCreate([
                'kecamatan_id' => $bukitBatu->id,
                'nama_desa' => $desa,
            ]);
        }
    }
}