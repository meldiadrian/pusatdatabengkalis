<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DaftarWebsiteDesa extends Model
{
    use HasFactory;

    protected $table = 'website_desas';

    public const MODE = ['online', 'offline'];
    public const STATUS = ['aktif', 'tidak aktif'];

    protected $fillable = [
        'kecamatan_id',
        'desa_ids',
        'websitedesa',
        'pembuat',
        'status',
        'keterangan'
    ];

    // 🔥 WAJIB untuk checkbox multiple
    protected $casts = [
        'desa_ids' => 'array',
    ];

    // ================= RELASI =================
    public function aplikasi()
    {
        return $this->belongsTo(DaftarAplikasiPerangkatDaerah::class, 'aplikasi_id');
    }


    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    // ================= HELPER =================

    // Ambil nama desa dari array desa_ids
    public function getDesaNamesAttribute()
    {
        if (!$this->desa_ids) {
            return '-';
        }

        return Desa::whereIn('id', $this->desa_ids)
            ->pluck('nama_desa')
            ->implode(', ');
    }
}
