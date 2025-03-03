<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogSheetController extends Controller
{
    public function view(Request $request)
    {
        $filepath = $request->query('filepath');

        if (Storage::disk('public')->exists($filepath)) {
            $fullPath = Storage::disk('public')->path($filepath); // Get absolute path

            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return abort(404, 'File not found');
    }
    public function download(Request $request)
    {
        $filepath = $request->query('filepath');

        if (Storage::disk('public')->exists($filepath)) {
            return Storage::disk('public')->download($filepath);
        }

        return abort(404, 'File not found');
    }
}
