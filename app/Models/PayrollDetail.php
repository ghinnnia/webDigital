<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $table = 'payroll_details';
    
    protected $fillable = [
        'payroll_period_id',
        'user_id',
        'karyawan_id',
        'nama_karyawan',
        'divisi',
        'gaji_per_bulan',
        'total_hari_kerja',
        'hari_hadir',
        'hari_alfa',
        'hari_sakit_tanpa_surat',
        'hari_izin_tanpa_ket',
        'hari_cuti',
        'hari_sakit_dengan_surat',
        'jam_lembur',
        'gaji_pokok',
        'potongan_tidak_hadir',
        'tunjangan_tetap',
        'nilai_kpa',
        'persentase_tunjangan_kinerja',
        'tunjangan_kinerja',
        'tarif_lembur_per_jam',
        'upah_lembur',
        'bonus',
        'tunjangan_lain',
        'potongan_bpjs',
        'potongan_lain',
        'total_gaji_bersih',
        'status',
        'catatan',
        'keterangan'
    ];
    
    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'potongan_tidak_hadir' => 'decimal:2',
        'tunjangan_tetap' => 'decimal:2',
        'tunjangan_kinerja' => 'decimal:2',
        'upah_lembur' => 'decimal:2',
        'bonus' => 'decimal:2',
        'tunjangan_lain' => 'decimal:2',
        'potongan_bpjs' => 'decimal:2',
        'potongan_lain' => 'decimal:2',
        'total_gaji_bersih' => 'decimal:2',
        'jam_lembur' => 'decimal:2',
    ];
    
    // Relasi ke PayrollPeriod (HARUS ADA)
    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }
    
    // Relasi ke User (karyawan)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    // Relasi ke Karyawan (opsional)
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}