<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahTalangMuandauSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Kecamatan Talang Muandau
        $talangMuandau = Kecamatan::firstOrCreate([
            'nama_kecamatan' => 'Talang Muandau',
        ]);

        // Daftar desa di Talang Muandau
        $desas = [
            'Serai Wangi',
            'Tasik Serai',
            'Tasik Serai Barat',
            'Tasik Serai Timur',
            'Beringin',
            'Koto Pait Beringin',
        ];

        // Insert semua desa
        foreach ($desas as $desa) {
            Desa::firstOrCreate([
                'kecamatan_id' => $talangMuandau->id,
                'nama_desa' => $desa,
            ]);
        }
    }
}
