<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $table = 'payroll_details';
    
    protected $fillable = [
        'payroll_period_id', 'user_id', 'karyawan_id', 'nama_karyawan', 'divisi',
        'gaji_per_bulan', 'total_hari_kerja', 'hari_hadir', 'hari_alfa',
        'hari_sakit_tanpa_surat', 'hari_izin_tanpa_ket', 'hari_cuti',
        'hari_sakit_dengan_surat', 'jam_lembur', 'gaji_pokok', 'potongan_tidak_hadir',
        'tunjangan_tetap', 'nilai_kpa', 'persentase_tunjangan_kinerja',
        'tunjangan_kinerja', 'tarif_lembur_per_jam', 'upah_lembur',
        'total_gaji_bersih', 'status', 'catatan'
    ];

    public function period()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function getRupiahGajiPokokAttribute()
    {
        return 'Rp ' . number_format($this->gaji_pokok, 0, ',', '.');
    }

    public function getRupiahTotalGajiAttribute()
    {
        return 'Rp ' . number_format($this->total_gaji_bersih, 0, ',', '.');
    }
}