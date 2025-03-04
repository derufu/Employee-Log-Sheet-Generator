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

}
