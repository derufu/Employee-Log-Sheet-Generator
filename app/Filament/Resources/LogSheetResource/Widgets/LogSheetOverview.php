<?php

namespace App\Filament\Resources\LogSheetResource\Widgets;

use App\Models\LogSheet;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LogSheetOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalLogSheets = LogSheet::count();
        $currentMonthLogSheets = LogSheet::whereMonth('created_at', now()->month)->count();

        return [
            Stat::make('Total Log Sheets', $totalLogSheets)
                ->description('Total number of log sheets')
                ->descriptionIcon('heroicon-o-document-text'),
            Stat::make('Current Month Log Sheets', $currentMonthLogSheets)
                ->description('Log sheets created this month')
                ->descriptionIcon('heroicon-o-calendar'),
        ];
    }
}
