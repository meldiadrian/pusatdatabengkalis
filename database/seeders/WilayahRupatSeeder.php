<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahRupatSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Kecamatan Rupat
        $rupat = Kecamatan::firstOrCreate([
            'nama_kecamatan' => 'Rupat',
        ]);

        // Daftar desa di Kecamatan Rupat
        $desas = [
            'Batu Panjang',
            'Darul Aman',
            'Hutan Panjang',
            'Makeruh',
            'Pancur Jaya',
            'Pangkalan Nyirih',
            'Parit Kebumen',
            'Pergam',
            'Sri Tanjung',
            'Suka Damai',
        ];

        // Insert semua desa
        foreach ($desas as $desa) {
            Desa::firstOrCreate([
                'kecamatan_id' => $rupat->id,
                'nama_desa' => $desa,
            ]);
        }
    }
}
