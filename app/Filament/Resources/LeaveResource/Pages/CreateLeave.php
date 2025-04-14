<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            if (isset($data['employee_ids']) && is_array($data['employee_ids'])) {
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
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('An error occurred while creating leave records.')
                ->danger()
                ->send();
            throw $e; // Rethrow to ensure Filament handles the validation properly
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
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

        if (isset($data['employee_ids'])) {
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
        }

        // Calculate the number of days for the leave
        $start_date = Carbon::parse($data['start_date']);
        $end_date = Carbon::parse($data['end_date']);
        $leave_days = $start_date->diffInDays($end_date) + 1;

        // Check leave type and apply restrictions
        $leaveLimits = [
            'vacation' => 15,
            'sick' => 15,
            'maternity' => 60,
            'paternity' => 7,
            'solo_parent' => 7,
            'special_privilege' => 3,
            'study' => 10,
            'rehabilitation' => 30,
            'special_leave_benefits_for_women' => 60,
        ];

        if (isset($leaveLimits[$data['type']]) && $leave_days > $leaveLimits[$data['type']]) {
            Notification::make()
                ->title('Error')
                ->body(ucfirst(str_replace('_', ' ', $data['type'])) . ' leave cannot exceed ' . $leaveLimits[$data['type']] . ' days.')
                ->danger()
                ->send();
            throw ValidationException::withMessages([
                'type' => ucfirst(str_replace('_', ' ', $data['type'])) . ' leave cannot exceed ' . $leaveLimits[$data['type']] . ' days.',
            ]);
        }

        if ($data['type'] === 'maternity' && $employee->gender !== 'female') {
            Notification::make()
                ->title('Error')
                ->body('Maternity leave is only applicable to female employees.')
                ->danger()
                ->send();
            throw ValidationException::withMessages(['type' => 'Maternity leave is only applicable to female employees.']);
        }

        if ($data['type'] === 'paternity' && $employee->gender !== 'male') {
            Notification::make()
                ->title('Error')
                ->body('Paternity leave is only applicable to male employees.')
                ->danger()
                ->send();
            throw ValidationException::withMessages(['type' => 'Paternity leave is only applicable to male employees.']);
        }

        if ($data['type'] === 'special_leave_benefits_for_women' && $employee->gender !== 'female') {
            Notification::make()
                ->title('Error')
                ->body('Special leave benefits for women are only applicable to female employees.')
                ->danger()
                ->send();
            throw ValidationException::withMessages(['type' => 'Special leave benefits for women are only applicable to female employees.']);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
