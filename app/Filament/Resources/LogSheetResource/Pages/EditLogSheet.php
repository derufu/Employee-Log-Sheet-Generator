<?php

namespace App\Filament\Resources\LogSheetResource\Pages;

use App\Filament\Resources\LogSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLogSheet extends EditRecord
{
    protected static string $resource = LogSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
