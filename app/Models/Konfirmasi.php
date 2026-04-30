<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konfirmasi extends Model
{
    public function pemohon()
    {
        return $this->belongsTo(Pemohon::class);
    }
}
