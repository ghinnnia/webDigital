<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLog extends Model
{
    protected $table = 'payroll_logs';
    
    protected $fillable = [
        'payroll_period_id', 'user_id', 'aksi', 'keterangan'
    ];

    public $timestamps = false;
    
    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function period()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}