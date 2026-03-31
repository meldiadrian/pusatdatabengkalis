<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DaftarAplikasiPerangkatDaerah extends Model
{

    use HasFactory;
    public const MODE = ['online', 'offline'];
    public const STATUS = ['aktif', 'tidak aktif'];


    protected $table = 'daftar_aplikasi_perangkat_daerahs';
    protected $fillable = [
        'unit_kerja_id',
        'nama_aplikasi',
        'mode',
        'jenis_aplikasi',
        'instansi_pemohon',
        'kecamatan_id',
        'desa_ids',
        'alamat_domain',
        'pembuat',
        'status',
        'keterangan',
    ];

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    protected $guarded = [];

    protected $casts = [
        'pembuat_diskominfotik' => 'boolean',
        'jenis_web' => 'boolean',
        'jenis_mobile' => 'boolean',
        'pemilik_pusat' => 'boolean',
        'pemilik_daerah' => 'boolean',
        'desa_ids' => 'array',

    ];
}
