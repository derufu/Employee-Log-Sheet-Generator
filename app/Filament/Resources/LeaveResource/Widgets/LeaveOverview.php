<?php

namespace App\Filament\Resources\LeaveResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Leave;

class LeaveOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Leaves', Leave::count()),
            Stat::make('Approved Leaves', Leave::where('status', 'approved')->count()),
            Stat::make('Pending Leaves', Leave::where('status', 'pending')->count()),
            Stat::make('Rejected Leaves', Leave::where('status', 'rejected')->count()),
        ];
    }
}
