<?php

namespace App\Filament\Resources\LogSheetResource\Pages;

use App\Filament\Resources\LogSheetResource;
use App\Filament\Resources\LogSheetResource\Widgets\LogSheetOverview;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Employee;
use App\Models\LogSheet;
use Carbon\Carbon;
use Filament\Forms;
use Illuminate\Support\Facades\Storage;

class ListLogSheets extends ListRecords
{
    protected static string $resource = LogSheetResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            LogSheetOverview::class,
        ];
    }

    protected function getHolidays($year)
    {
        return [
            ['Name' => "New Year's Day", 'Date' => "$year-01-01"],
            ['Name' => "Maundy Thursday", 'Date' => date('Y-m-d', strtotime("$year-03-24"))],
            ['Name' => "Good Friday", 'Date' => date('Y-m-d', strtotime("$year-03-25"))],
            ['Name' => "Black Saturday", 'Date' => date('Y-m-d', strtotime("$year-03-26"))],
            ['Name' => "Independence Day", 'Date' => "$year-06-12"],
            ['Name' => "National Heroes Day", 'Date' => date('Y-m-d', strtotime("last monday of $year-08"))],
            ['Name' => "Bonifacio Day", 'Date' => "$year-11-30"],
            ['Name' => "Christmas Day", 'Date' => "$year-12-25"],
            ['Name' => "Rizal Day", 'Date' => "$year-12-30"],
            ['Name' => "New Year's Eve", 'Date' => "$year-12-31"],
            ['Name' => "Chinese New Year", 'Date' => date('Y-m-d', strtotime("$year-01-01 +15 days"))],
            ['Name' => "Eid'l Fitr (Feast of Ramadan)", 'Date' => date('Y-m-d', strtotime("$year-07-20"))],
            ['Name' => "Eid'l Adha (Feast of Sacrifice)", 'Date' => date('Y-m-d', strtotime("$year-07-09"))],
            ['Name' => "Ninoy Aquino Day", 'Date' => "$year-08-21"],
            ['Name' => "All Saints' Day (Undas)", 'Date' => "$year-11-01"],
        ];
    }

    private function isHoliday($date)
    {
        $year = $date->format('Y');
        $holidays = $this->getHolidays($year);
        $formattedDate = $date->format('Y-m-d');

        foreach ($holidays as $holiday) {
            if ($formattedDate === $holiday['Date']) {
                return true;
            }
        }
        return false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generateLogSheet')
                ->label('Generate Log Sheet')
                ->form([
                    Forms\Components\Select::make('position_type')
                        ->options([
                            'job_order' => 'Job Order',
                            'coterminous' => 'Coterminous',
                            'contract_of_service' => 'Contract of Service',
                            'plantilla' => 'Plantilla',
                        ])
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('employee_ids', [])),
                    Forms\Components\Select::make('employee_ids')
                        ->label('Employees')
                        ->multiple()
                        ->options(function (callable $get) {
                            $position_type = $get('position_type');
                            return Employee::where('position_type', $position_type)
                                ->get()
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
                            $position_type = $get('position_type');
                            $employees = Employee::where('position_type', $position_type)
                                ->get()
                                ->mapWithKeys(function ($employee) {
                                    $middleInitial = $employee->middle_name ? substr($employee->middle_name, 0, 1) . '.' : '';
                                    return [$employee->id => $employee->first_name . ' ' . $middleInitial . ' ' . $employee->last_name];
                                });
                            return ['all' => 'All'] + $employees->toArray();
                        }),
                    Forms\Components\DatePicker::make('month')
                        ->label('Select Month')
                        ->required()
                        ->format('Y-m')
                        ->displayFormat('F Y')
                        ->default(now()->format('Y-m')),
                ])
                ->action(function (array $data) {
                    $month = Carbon::parse($data['month'])->format('m');
                    $year = Carbon::parse($data['month'])->format('Y');
                    $current_time = date('Y-m-d_H-i-s_A'); // Fix timestamp format
                    $position_type = $data['position_type'];
                    $employee_ids = $data['employee_ids'];

                    if (in_array('all', $employee_ids)) {
                        $employee_ids = Employee::where('position_type', $position_type)->pluck('id')->toArray();
                    }

                    $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    $weekdays = [];

                    $employees = Employee::whereIn('id', $employee_ids)
                        ->where('position_type', $position_type)
                        ->with(['leaves' => function ($query) use ($year, $month) {
                            $query->whereMonth('start_date', $month)
                                ->whereYear('end_date', $year)
                                ->where('status', 'approved');
                        }])
                        ->with(['workFromHomes' => function ($query) use ($year, $month) {
                            $query->whereMonth('start_date', $month)
                                ->whereYear('end_date', $year);
                        }])
                        ->orderBy('last_name', 'asc')
                        ->get();
                    // dd($employees);
                    for ($day = 1; $day <= $numDays; $day++) {
                        $dayOfWeek = date('N', strtotime("$year-$month-$day"));
                        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                            $formattedDate = date('Y-m-d', strtotime("$year-$month-$day"));
                            $weekdays[] = $formattedDate;
                        }
                    }

                    $options = new Options();
                    $options->set('isHtml5ParserEnabled', true);
                    $options->set('isPhpEnabled', true);
                    $dompdf = new Dompdf($options);

                    $html = view('filament.export.log-sheet', compact('employees', 'weekdays'))->render();
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('letter', 'portrait');
                    $dompdf->render();

                    $output = $dompdf->output();
                    $directory = "logsheets/{$year}/{$month}";
                    Storage::disk('public')->makeDirectory($directory);

                    $filePath = "{$directory}/log_sheet_{$position_type}_{$current_time}.pdf";
                    Storage::disk('public')->put($filePath, $output);

                    LogSheet::create([
                        'filename' => basename($filePath),
                        'filepath' => $filePath,
                        'year' => $year,
                        'month' => $month,
                    ]);

                    return response()->download(Storage::disk('public')->path($filePath));
                })
        ];
    }
}
