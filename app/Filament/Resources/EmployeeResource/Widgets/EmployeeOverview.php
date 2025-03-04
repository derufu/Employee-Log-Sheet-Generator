<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $inactiveEmployees = Employee::where('status', 'inactive')->count();

        $positionTypes = Employee::select('position_type')
            ->distinct()
            ->pluck('position_type');

        $stats = [
            Stat::make('Total Employees', $totalEmployees)
                ->description('Total number of employees'),
            Stat::make('Active Employees', $activeEmployees)
                ->description('Number of active employees'),
            Stat::make('Inactive Employees', $inactiveEmployees)
                ->description('Number of inactive employees'),
        ];

        foreach ($positionTypes as $positionType) {
            $count = Employee::where('position_type', $positionType)->count();
            $stats[] = Stat::make(ucfirst($positionType) . ' Employees', $count)
                ->description('Number of ' . $positionType . ' employees');
        }

        return $stats;
    }
}
