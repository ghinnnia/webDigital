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
        'user_id', 'nama', 'role', 'divisi', 'divisi_id', 'tim_id',
        'gaji', 'kontrak_mulai', 'kontrak_selesai', 'alamat',
        'kontak', 'foto', 'email', 'status_kerja', 'status_karyawan',
        'tunjangan_tetap_ids', 'tunjangan_tidak_tetap_ids'
    ];

    protected $casts = [
        // Laravel otomatis handle json_encode/decode jika di-cast ke array
        'tunjangan_tetap_ids' => 'array',
        'tunjangan_tidak_tetap_ids' => 'array',
        'kontrak_mulai' => 'datetime',
        'kontrak_selesai' => 'datetime',
        'gaji' => 'integer',
    ];

    // Relasi
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function divisiRelation() { return $this->belongsTo(Divisi::class, 'divisi_id'); }
    public function tim() { return $this->belongsTo(Tim::class, 'tim_id'); }

    // --- RELASI TUNJANGAN ---
    public function tunjanganMaster()
    {
        return $this->belongsToMany(TunjanganMaster::class, 'karyawan_tunjangan', 'karyawan_id', 'tunjangan_id');
    }

    // Relasi untuk eager loading fixed allowances
    public function tunjanganTetapRelation()
    {
        return $this->tunjanganMaster()->where('tipe', 'bulanan');
    }

    // Relasi untuk eager loading variable allowances
    public function tunjanganTidakTetapRelation()
    {
        return $this->tunjanganMaster()->whereIn('tipe', ['bonus', 'insentif']);
    }

    public function tunjanganTetap()
    {
        return $this->tunjanganMaster()->where('tipe', 'bulanan');
    }

    public function tunjanganTidakTetap()
    {
        return $this->tunjanganMaster()->whereIn('tipe', ['bonus', 'insentif']);
    }

    // --- ACCESSOR UNTUK TUNJANGAN ---
    public function getTunjanganTetapListAttribute()
    {
        return $this->tunjanganMaster()->where('tipe', 'bulanan')->get();
    }

    public function getTunjanganTetapTotalAttribute()
    {
        return $this->tunjanganMaster()->where('tipe', 'bulanan')->sum('nominal');
    }

    public function getTunjanganTidakTetapListAttribute()
    {
        return $this->tunjanganMaster()->whereIn('tipe', ['bonus', 'insentif'])->get();
    }

    public function getTunjanganTidakTetapTotalAttribute()
    {
        return $this->tunjanganMaster()->whereIn('tipe', ['bonus', 'insentif'])->sum('nominal');
    }

    // --- LOGIKA OTOMATIS NONAKTIF KONTRAK ---
    protected static function boot()
    {
        parent::boot();
        static::retrieved(function ($model) {
            if ($model->status_karyawan === 'kontrak' && $model->kontrak_selesai) {
                if ($model->kontrak_selesai->isPast() && $model->status_kerja !== 'nonaktif') {
                    $model->status_kerja = 'nonaktif';
                    $model->saveQuietly();
                }
            }
        });
    }
}