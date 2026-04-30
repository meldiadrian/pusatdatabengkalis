<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemohon extends Model
{
    protected $fillable = [
        'instansi_pemohon',
        'data_diminta',
        'tujuan_penggunaan',
        'opd_tujuan',
        'upload_surat',
        'keterangan',
    ];

    public function pemilikData()
    {
        return $this->hasMany(PemilikData::class);
    }

    public function suratBalasan()
    {
        return $this->hasMany(SuratBalasan::class);
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'instansi_pemohon');
    }

    public function unitKerjaTujuan()
    {
        return $this->belongsTo(UnitKerja::class, 'opd_tujuan');
    }

    public function konfirmasi()
    {
        return $this->hasOne(Konfirmasi::class);
    }

    
}
