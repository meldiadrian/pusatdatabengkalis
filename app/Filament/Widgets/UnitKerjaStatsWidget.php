<?php

namespace App\Filament\Widgets;

use App\Models\UnitKerja;
use Filament\Widgets\Widget;

class UnitKerjaStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.unit-kerja-stats-widget';

    protected int | string | array $columnSpan = 1;

    public function getTotal()
    {
        return UnitKerja::count();
    }
}
