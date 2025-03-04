<?php

namespace App\Filament\Resources\WorkFromHomeResource\Pages;

use App\Filament\Resources\WorkFromHomeResource;
use App\Models\WorkFromHome;
use App\Models\Employee;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Filament\Forms;

class CreateWorkFromHome extends CreateRecord
{
    protected static string $resource = WorkFromHomeResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        if (is_array($data['employee_ids'])) {
            foreach ($data['employee_ids'] as $employeeId) {
                WorkFromHome::create([
                    'employee_id' => $employeeId,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'reason' => $data['reason'],
                ]);
            }
            Notification::make()
                ->title('Success')
                ->body('Work from home records created successfully.')
                ->success()
                ->send();
            return new WorkFromHome(); // Return an empty model to satisfy the method signature
        }

        return WorkFromHome::create($data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['start_date']) && isset($data['end_date']) && $data['end_date'] < $data['start_date']) {
            throw ValidationException::withMessages([
                'end_date' => 'The end date cannot be before the start date.',
            ]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
