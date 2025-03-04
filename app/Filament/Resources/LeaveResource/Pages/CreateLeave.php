<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Filament\Forms;
class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        if (is_array($data['employee_ids'])) {
            foreach ($data['employee_ids'] as $employeeId) {
                Leave::create([
                    'employee_id' => $employeeId,
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'reason' => $data['reason'],
                    'type' => $data['type'],
                    'status' => $data['status'],
                ]);
            }
            Notification::make()
                ->title('Success')
                ->body('Leave records created successfully.')
                ->success()
                ->send();
            return new Leave(); // Return an empty model to satisfy the method signature
        }

        return Leave::create($data);
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
