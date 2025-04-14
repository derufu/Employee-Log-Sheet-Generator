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
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Leave Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Employees')
                    ->options(Employee::all()->pluck('full_name', 'id'))
                    ->searchable()
                    ->preload()
                    ->columnSpan(2),

                Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->rules(['required', 'date']),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->rules(['required', 'date', 'after_or_equal:start_date']),

                Forms\Components\Select::make('type')
                    ->options([
                        'sick' => 'Sick',
                        'vacation' => 'Vacation',
                        'maternity' => 'Maternity',
                        'paternity' => 'Paternity',
                        'bereavement' => 'Bereavement',
                        'solo_parent' => 'Solo Parent',
                        'special_privilege' => 'Special Privilege',
                        'study' => 'Study',
                        'rehabilitation' => 'Rehabilitation',
                        'special_leave_benefits_for_women' => 'Special Leave Benefits for Women',
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
            ])
            ->statePath('data');
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
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
