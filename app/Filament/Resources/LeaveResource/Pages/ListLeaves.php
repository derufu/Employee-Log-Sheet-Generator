<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use App\Filament\Resources\LeaveResource\Widgets\LeaveChartWidget;
use App\Filament\Resources\LeaveResource\Widgets\LeaveOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LeaveOverview::class,
        ];
    }
}
