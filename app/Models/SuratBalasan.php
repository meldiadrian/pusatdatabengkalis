<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratBalasan extends Model
{
    protected $table = 'surat_balasan';

    protected $fillable = [
        'pemohon_id',
        'pemilik_data_id',
        'upload_balasan',
        'keterangan',
    ];

    public function pemohon()
    {
        return $this->belongsTo(Pemohon::class);
    }

    public function pemilikData()
    {
        return $this->belongsTo(PemilikData::class);
    }
}
