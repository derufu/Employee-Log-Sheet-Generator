<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LogSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'filepath',
        'year',
        'month',
    ];
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($logSheet) {
            $filePath = "logsheets/{$logSheet->year}/{$logSheet->month}/{$logSheet->filename}";
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
        });
    }
}
