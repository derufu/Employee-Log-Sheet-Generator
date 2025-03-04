<?php

namespace App\Filament\Resources\WorkFromHomeResource\Pages;

use App\Filament\Resources\WorkFromHomeResource;
use App\Filament\Resources\WorkFromHomeResource\Widgets\WorkFromHomeOverview;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkFromHomes extends ListRecords
{
    protected static string $resource = WorkFromHomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WorkFromHomeOverview::class,
        ];
    }
}
