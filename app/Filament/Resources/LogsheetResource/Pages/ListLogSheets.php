<?php

namespace App\Filament\Resources\LogSheetResource\Pages;

use App\Filament\Resources\LogSheetResource;
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
                        ->required(),
                    Forms\Components\DatePicker::make('month')
                        ->label('Select Month')
                        ->required()
                        ->format('Y-m')
                        ->displayFormat('F Y')
                        ->default(now()->format('Y-m')),
                ])
                // ->action(function (array $data) {
                //     $month = Carbon::parse($data['month'])->format('m');
                //     $year = Carbon::parse($data['month'])->format('Y');
                //     $current_time = date('Y-m-d h:i:s A');
                //     $position_type = $data['position_type'];
                //     $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                //     $weekdays = [];
                //     $employees = Employee::where('position_type', $position_type)
                //         ->with(['leaves' => function ($query) use ($year, $month) {
                //             $query->whereMonth('start_date', $month)
                //                 ->whereYear('end_date', $year)
                //                 ->where('status', 'approved');
                //         }])
                //         ->with(['workFromHomes' => function ($query) use ($year, $month) {
                //             $query->whereMonth('start_date', $month)
                //                 ->whereYear('end_date', $year);
                //         }])
                //         ->orderBy('last_name', 'asc')
                //         ->get();

                //     for ($day = 1; $day <= $numDays; $day++) {
                //         $dayOfWeek = date('N', strtotime("$year-$month-$day"));
                //         if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                //             $formattedDate = date('Y-m-d', strtotime("$year-$month-$day"));
                //             $weekdays[] = $formattedDate;
                //         }
                //     }

                //     $options = new Options();
                //     $options->set('isHtml5ParserEnabled', true);
                //     $options->set('isPhpEnabled', true);
                //     $dompdf = new Dompdf($options);

                //     $html = view('filament.export.log-sheet', compact('employees', 'weekdays'))->render();
                //     $dompdf->loadHtml($html);
                //     $dompdf->setPaper('letter', 'portrait');
                //     $dompdf->render();

                //     $output = $dompdf->output();
                //     $filePath = "logsheets/{$year}/{$month}/log_sheet_{$position_type}_{$current_time}.pdf";
                //     Storage::disk('public')->makeDirectory("logsheets/{$year}/{$month}");
                //     Storage::disk('public')->put($filePath, $output);
                //     LogSheet::create([
                //         'filename' => basename($filePath), // Extracts just the filename
                //         'filepath' => $filePath,
                //         'year' => $year,
                //         'month' => $month,
                //     ]);


                //     return response()->download(storage_path("app/public/{$filePath}"));
                // })
                // ->icon('heroicon-o-document-text'),

                ->action(function (array $data) {
                    $month = Carbon::parse($data['month'])->format('m');
                    $year = Carbon::parse($data['month'])->format('Y');
                    $current_time = date('Y-m-d_H-i-s_A'); // Fix timestamp format
                    $position_type = $data['position_type'];
                    $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    $weekdays = [];

                    $employees = Employee::where('position_type', $position_type)
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
                ->icon('heroicon-o-document-text'),

        ];
    }
}
