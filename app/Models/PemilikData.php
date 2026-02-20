<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UnitKerja;

class PemilikData extends Model
{

    protected $table = 'pemilik_data';

    public function unitKerja()

    {
        return $this->belongsTo(UnitKerja::class, 'instansi_pemohon');
    }

    public function unitKerjaTujuan()
    {
        return $this->belongsTo(UnitKerja::class, 'opd_tujuan');
    }

    public function pemohon()
    {
        return $this->belongsTo(Pemohon::class, 'pemohon_id');
    }

    public function suratBalasan()
    {
        return $this->hasMany(SuratBalasan::class, 'pemilik_data_id');
    }
}
