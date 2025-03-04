<?php

namespace App\Filament\Resources\LeaveResource\Widgets;

use App\Models\Leave;
use Filament\Widgets\ChartWidget;

class LeaveChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Leave Statistics';

    protected function getData(): array
    {
        $leaveCounts = Leave::selectRaw('employee_id, COUNT(*) as count')
            ->groupBy('employee_id')
            ->pluck('count', 'employee_id')
            ->toArray();

        $employeeNames = Leave::with('employee')
            ->get()
            ->pluck('employee.first_name', 'employee_id')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Leaves Taken',
                    'data' => array_values($leaveCounts),
                ],
            ],
            'labels' => array_values($employeeNames),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
