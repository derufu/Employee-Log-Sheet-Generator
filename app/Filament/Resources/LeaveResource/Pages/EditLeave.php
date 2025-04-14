<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use App\Models\Employee;

class EditLeave extends EditRecord
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['start_date'], $data['end_date']) && $data['end_date'] < $data['start_date']) {
            Notification::make()
                ->title('Error')
                ->body('The end date cannot be before the start date.')
                ->danger()
                ->send();
            throw ValidationException::withMessages([
                'end_date' => 'The end date cannot be before the start date.',
            ]);
        }

        if (array_key_exists('employee_ids', $data) && isset($data['employee_ids'])) {
            $employee = Employee::find($data['employee_ids']);
            if (!$employee) {
                Notification::make()
                    ->title('Error')
                    ->body('Employee not found.')
                    ->danger()
                    ->send();
                throw ValidationException::withMessages([
                    'employee_ids' => 'Employee not found.',
                ]);
            }

            // Ensure $employee is a single instance, not a collection
            if (is_array($employee)) {
                $employee = $employee[0];
            }

            // Ensure the leave record belongs to the selected employee
            if ($this->record->employee_id !== $employee->id) {
                Notification::make()
                    ->title('Error')
                    ->body('You can only edit leave records for the selected employee.')
                    ->danger()
                    ->send();
                throw ValidationException::withMessages([
                    'employee_ids' => 'You can only edit leave records for the selected employee.',
                ]);
            }
        }

        return $data;
    }
}
