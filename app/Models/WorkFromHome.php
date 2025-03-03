<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkFromHome extends Model
{
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'reason',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
