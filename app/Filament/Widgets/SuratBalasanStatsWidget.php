<?php

namespace App\Filament\Widgets;

use App\Models\SuratBalasan;
use Filament\Widgets\Widget;

class SuratBalasanStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.surat-balasan-stats-widget';

    protected int | string | array $columnSpan = 1;

    public function getTotal()
    {
        if (auth()->user()->isAdmin()) {
            return SuratBalasan::count();
        }

        return SuratBalasan::whereHas('pemohon', function ($q) {
            $q->whereHas('unitKerja', function ($subQ) {
                $subQ->where('unit_kerjas.id', auth()->user()->unit_kerja_id);
            });
        })->count();
    }
}
