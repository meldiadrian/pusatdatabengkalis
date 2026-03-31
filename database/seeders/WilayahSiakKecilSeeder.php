<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahSiakKecilSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Kecamatan Siak Kecil
        $siakKecil = Kecamatan::firstOrCreate([
            'nama_kecamatan' => 'Siak Kecil',
        ]);

        // Daftar desa di Kecamatan Siak Kecil
        $desas = [
            'Bandar Jaya',
            'Koto Raja',
            'Langkat',
            'Lubuk Gaung',
            'Muara Dua',
            'Sadar Jaya',
            'Sungai Linau',
            'Sungai Siput',
            'Tanjung Belit',
            'Tanjung Damai',
        ];

        // Insert semua desa
        foreach ($desas as $desa) {
            Desa::firstOrCreate([
                'kecamatan_id' => $siakKecil->id,
                'nama_desa' => $desa,
            ]);
        }
    }
}
