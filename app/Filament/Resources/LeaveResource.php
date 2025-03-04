<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveResource\Pages;
use App\Models\Employee;
use App\Models\Leave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Leave Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_ids')
                    ->label('Employees')
                    ->multiple()
                    ->options(function (callable $get) {
                        return Employee::get()
                            ->mapWithKeys(function ($employee) {
                                $middleInitial = $employee->middle_name ? substr($employee->middle_name, 0, 1) . '.' : '';
                                return [$employee->id => $employee->first_name . ' ' . $middleInitial . ' ' . $employee->last_name];
                            });
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if (in_array('all', $state)) {
                            $set('employee_ids', Employee::pluck('id')->toArray());
                        }
                    })
                    ->placeholder('Select employees or choose "All"')
                    ->options(function (callable $get) {
                        $employees = Employee::get()
                            ->mapWithKeys(function ($employee) {
                                $middleInitial = $employee->middle_name ? substr($employee->middle_name, 0, 1) . '.' : '';
                                return [$employee->id => $employee->first_name . ' ' . $middleInitial . ' ' . $employee->last_name];
                            });
                        return ['all' => 'All'] + $employees->toArray();
                    }),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        'sick' => 'Sick',
                        'vacation' => 'Vacation',
                        'maternity' => 'Maternity',
                        'paternity' => 'Paternity',
                        'bereavement' => 'Bereavement',
                        'others' => 'Others',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('reason')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('reason')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
