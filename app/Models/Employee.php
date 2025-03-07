<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'extension_name',
        'suffix',
        'birthdate',
        'employee_id',
        'email',
        'contact_number',
        'gender',
        'position_type',
        'position',
        'status',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_address',
        'image'
    ];

    public function workFromHomes()
    {
        return $this->hasMany(WorkFromHome::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function getFullNameAttribute()
    {
        $middleInitial = $this->middle_name ? substr($this->middle_name, 0, 1) . '.' : '';
        return "{$this->first_name} {$middleInitial} {$this->last_name}";
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($employee) {
            if (request()->hasFile('image')) {
                $employee->image = request()->file('image')->store('employee-images', 'public');
            }
        });
    }
}
