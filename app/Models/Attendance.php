<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances'; // Explicitly set table name
    protected $connection = 'mysql'; // Use db_agency connection

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'status_type',
        'late_minutes',
        'reason',
        'location',
        'purpose',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'time',
        'check_out' => 'time',
        'late_minutes' => 'integer',
    ];

    // Scope untuk mendapatkan data hari ini
    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->format('Y-m-d'));
    }

    // Scope untuk mendapatkan data berdasarkan employee
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // Scope untuk mendapatkan data bulan ini
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year);
    }

    // Relationship dengan employee (jika ada tabel employees)
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}