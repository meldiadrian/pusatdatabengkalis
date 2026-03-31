<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kecamatan;
use App\Models\Desa;

class WilayahMandauSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Kecamatan Mandau
        $mandau = Kecamatan::create([
            'nama_kecamatan' => 'Mandau',
        ]);

        // Daftar desa di Mandau
        $desas = [
            'Air Jamban',
            'Babussalam',
            'Balik Alam',
            'Batang Serosa',
            'Duri Barat',
            'Duri Timur',
            'Gajah Sakti',
            'Harapan Baru',
            'Pematang Pudu',
            'Talang Mandi',
        ];

        // Insert semua desa
        foreach ($desas as $desa) {
            Desa::create([
                'kecamatan_id' => $mandau->id,
                'nama_desa' => $desa,
            ]);
        }
    }
}
