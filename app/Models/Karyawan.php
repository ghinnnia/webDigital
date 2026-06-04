<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'user_id',
        'nama',
        'role',
        'divisi',
        'divisi_id',
        'tim_id',
        'gaji',
        'kontrak_mulai',
        'kontrak_selesai',
        'alamat',
        'kontak',
        'foto',
        'email',
        'status_kerja',
        'status_karyawan'
    ];

    protected $casts = [
        'kontrak_mulai' => 'datetime',
        'kontrak_selesai' => 'datetime',
        'gaji' => 'integer',
    ];

    // =========================
    // RELASI
    // =========================

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function divisiRelation()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function tim()
    {
        return $this->belongsTo(Tim::class, 'tim_id');
    }

    /**
     * Relasi ke tabel tunjangan_karyawan
     */
    public function tunjanganKaryawan()
    {
        return $this->hasMany(TunjanganKaryawan::class, 'karyawan_id');
    }

    /**
     * Relasi utama ke Tunjangan Master
     */
    public function tunjangan()
    {
        return $this->belongsToMany(
            TunjanganMaster::class,
            'tunjangan_karyawan',
            'karyawan_id',
            'tunjangan_id'
        )
        ->withPivot(
            'id',
            'nominal',
            'bulan',
            'tahun',
            'diberikan'
        )
        ->withTimestamps();
    }

    /**
     * Alias relasi lama
     */
    public function tunjanganMaster()
    {
        return $this->tunjangan();
    }

    /**
     * Relasi ke tabel karyawan_tunjangan (pivot simpel, tanpa bulan/tahun)
     * Digunakan untuk menyimpan tunjangan default/template karyawan
     */
    public function tunjanganDefault()
    {
        return $this->belongsToMany(
            TunjanganMaster::class,
            'karyawan_tunjangan',
            'karyawan_id',
            'tunjangan_id'
        )->withTimestamps();
    }

    /**
     * Tunjangan berdasarkan periode
     */
    public function tunjanganPeriode($bulan, $tahun)
    {
        return $this->tunjangan()
            ->wherePivot('bulan', $bulan)
            ->wherePivot('tahun', $tahun)
            ->wherePivot('diberikan', 1);
    }

    /**
     * Semua tunjangan tetap
     */
    public function tunjanganTetap()
    {
        return $this->tunjangan()
            ->where('tipe', 'bulanan');
    }

    /**
     * Tunjangan tetap bulan ini
     */
    public function tunjanganTetapBulanIni()
    {
        $now = Carbon::now();

        return $this->tunjangan()
            ->where('tipe', 'bulanan')
            ->wherePivot('bulan', $now->month)
            ->wherePivot('tahun', $now->year)
            ->wherePivot('diberikan', true);
    }

    /**
     * Semua tunjangan bonus/insentif
     */
    public function tunjanganTidakTetap()
    {
        return $this->tunjangan()
            ->whereIn('tipe', ['bonus', 'insentif']);
    }

    /**
     * Tunjangan bonus/insentif bulan ini
     */
    public function tunjanganTidakTetapBulanIni()
    {
        $now = Carbon::now();

        return $this->tunjangan()
            ->whereIn('tipe', ['bonus', 'insentif'])
            ->wherePivot('bulan', $now->month)
            ->wherePivot('tahun', $now->year)
            ->wherePivot('diberikan', true);
    }

    // =========================
    // ACCESSOR
    // =========================

    public function getTunjanganTetapListAttribute()
    {
        $now = Carbon::now();

        return TunjanganMaster::where('tipe', 'bulanan')
            ->whereHas('tunjanganKaryawan', function ($q) use ($now) {
                $q->where('karyawan_id', $this->id)
                    ->where('bulan', $now->month)
                    ->where('tahun', $now->year)
                    ->where('diberikan', 1);
            })
            ->get();
    }

    public function getTunjanganTidakTetapListAttribute()
    {
        $now = Carbon::now();

        return TunjanganMaster::whereIn('tipe', ['bonus', 'insentif'])
            ->whereHas('tunjanganKaryawan', function ($q) use ($now) {
                $q->where('karyawan_id', $this->id)
                    ->where('bulan', $now->month)
                    ->where('tahun', $now->year)
                    ->where('diberikan', 1);
            })
            ->get();
    }

    public function getTunjanganTetapTotalAttribute()
    {
        return $this->tunjangan_tetap_list->sum('nominal');
    }

    public function getTunjanganTidakTetapTotalAttribute()
    {
        return $this->tunjangan_tidak_tetap_list->sum('nominal');
    }

    // =========================
    // AUTO NONAKTIF KONTRAK
    // =========================

    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($model) {
            if (
                $model->status_karyawan === 'kontrak' &&
                $model->kontrak_selesai
            ) {
                if (
                    $model->kontrak_selesai->isPast() &&
                    $model->status_kerja !== 'nonaktif'
                ) {
                    $model->status_kerja = 'nonaktif';
                    $model->saveQuietly();
                }
            }
        });
    }
}
