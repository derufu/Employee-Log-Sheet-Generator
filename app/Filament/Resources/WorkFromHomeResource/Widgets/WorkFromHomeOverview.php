<?php

namespace App\Filament\Resources\WorkFromHomeResource\Widgets;

use App\Filament\Resources\WorkFromHomeResource;
use App\Models\WorkFromHome;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WorkFromHomeOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Work From Home', WorkFromHome::count()),
        ];
    }
}
