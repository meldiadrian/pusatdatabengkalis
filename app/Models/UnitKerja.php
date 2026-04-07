<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    public function users()
    {
        return $this->hasMany(User::class, 'unit_kerja_id');
    }

    public function daftarAplikasi()
    {
        return $this->hasMany(DaftarAplikasiPerangkatDaerah::class, 'unit_kerja_id');
    }


    public function websiteDesas()
    {
        return $this->hasMany(DaftarWebsiteDesa::class, 'unit_kerja_id');
    }
}
