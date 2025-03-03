<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Resources\Pages\Page;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class EmployeeLogSheet extends Page
{
    protected static string $resource = EmployeeResource::class;

    protected static string $view = 'export.employee-log-sheet';

    public $month;

    public function mount(Request $request)
    {
        $this->month = $request->query('month');
    }

    public function getEmployees()
    {
        $month = Carbon::parse($this->month);
        return Employee::with(['workFromHomes' => function ($query) use ($month) {
            $query->whereMonth('date_start', $month->month)
                ->whereYear('date_start', $month->year);
        }, 'leaves' => function ($query) use ($month) {
            $query->whereMonth('start_date', $month->month)
                ->whereYear('start_date', $month->year);
        }])->get();
    }
    public function generatePdf()
    {
        $data = []; // Fetch your data here
        $pdf = FacadePdf::loadView('export.employee-log-sheet-pdf', $data);
        return $pdf->download('employee-log-sheet.pdf');
    }
}
