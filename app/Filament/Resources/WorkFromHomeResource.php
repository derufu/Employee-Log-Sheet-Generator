<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkFromHomeResource\Pages;
use App\Models\WorkFromHome;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;

class WorkFromHomeResource extends Resource
{
    protected static ?string $model = WorkFromHome::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

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
                })
                ->columnSpan(2), // Add this line to span 2 columns
            Forms\Components\DatePicker::make('start_date')
                ->default(Carbon::now()->toDateString())
                ->required(),
            Forms\Components\DatePicker::make('end_date')
                ->required(),
            Forms\Components\TextInput::make('reason')
                ->maxLength(255),
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
                Tables\Columns\TextColumn::make('reason')
                    ->searchable(),
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
            'index' => Pages\ListWorkFromHomes::route('/'),
            'create' => Pages\CreateWorkFromHome::route('/create'),
            'edit' => Pages\EditWorkFromHome::route('/{record}/edit'),
        ];
    }
}
