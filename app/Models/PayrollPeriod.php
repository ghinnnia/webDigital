<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    protected $table = 'payroll_periods';
    
    protected $fillable = [
        'bulan', 'tahun', 'tanggal_mulai', 'tanggal_selesai',
        'tanggal_pembayaran', 'status', 'dibuat_oleh', 'disetujui_oleh',
        'disetujui_at', 'dibayar_at', 'catatan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_pembayaran' => 'date',
        'disetujui_at' => 'datetime',
        'dibayar_at' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function logs()
    {
        return $this->hasMany(PayrollLog::class);
    }

    public function dibuatOleh()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function getNamaPeriodeAttribute()
    {
        $bulanIndonesia = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanIndonesia[$this->bulan] . ' ' . $this->tahun;
    }

    public static function getTotalPayrollGajiBersih()
    {
        return static::with('details')->get()->sum(function ($period) {
            return $period->details->sum('total_gaji_bersih');
        });
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'processed' => 'info',
            'approved' => 'success',
            'paid' => 'primary'
        ];
        $labels = [
            'draft' => 'Draft',
            'processed' => 'Diproses',
            'approved' => 'Disetujui',
            'paid' => 'Dibayar'
        ];
        $badge = $badges[$this->status] ?? 'secondary';
        $label = $labels[$this->status] ?? $this->status;
        return "<span class='badge bg-{$badge}'>{$label}</span>";
    }
}