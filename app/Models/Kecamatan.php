<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $fillable = [
        'nama_kecamatan',
    ];

    public function desas() // 🔥 pakai plural
    {
        return $this->hasMany(Desa::class);
    }
}
