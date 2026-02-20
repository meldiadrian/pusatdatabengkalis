<?php

namespace App\Filament\Widgets;

use App\Models\Pemohon;
use Filament\Widgets\Widget;

class SuratPermohonanStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.surat-permohonan-stats-widget';

    protected int | string | array $columnSpan = 1;

    public function getTotal()
    {
        if (auth()->user()->isAdmin()) {
            return Pemohon::count();
        }

        return Pemohon::whereHas('unitKerja', function ($q) {
            $q->where('unit_kerjas.id', auth()->user()->unit_kerja_id);
        })->count();
    }
}
