<?php

namespace App\Services;

use App\Models\User;
use App\Models\PayrollPeriod;
use App\Models\PayrollDetail;
use App\Models\PayrollAllowance;
use App\Models\KpaTunjanganRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollCalculator
{
    protected $period;
    protected $tanggalMulai;
    protected $tanggalSelesai;
    protected $totalHariKerja;

    public function __construct(PayrollPeriod $period)
    {
        $this->period = $period;
        $this->tanggalMulai = Carbon::parse($period->tanggal_mulai);
        $this->tanggalSelesai = Carbon::parse($period->tanggal_selesai);
        $this->totalHariKerja = $this->hitungTotalHariKerja();
    }

    /**
     * Hitung total hari kerja (Senin-Jumat) dalam periode
     */
    protected function hitungTotalHariKerja()
    {
        $hariKerja = 0;
        $tanggal = $this->tanggalMulai->copy();
        
        while ($tanggal <= $this->tanggalSelesai) {
            // Hari Senin=1, Selasa=2, ..., Minggu=7
            // Weekend: Sabtu=6, Minggu=7
            $dayOfWeek = (int) $tanggal->format('N');
            if ($dayOfWeek < 6) { // Senin s/d Jumat
                $hariKerja++;
            }
            $tanggal->addDay();
        }
        
        return $hariKerja;
    }

    /**
     * Proses gaji untuk semua karyawan aktif
     */
    public function prosesGajiSemuaKaryawan()
    {
        // Ambil karyawan aktif (role karyawan atau manager_divisi)
        $karyawanAktif = User::whereIn('role', ['karyawan', 'manager_divisi'])
            ->where('status_kerja', 'aktif')
            ->get();

        $results = [];
        
        foreach ($karyawanAktif as $karyawan) {
            $results[] = $this->prosesGajiKaryawan($karyawan);
        }
        
        return $results;
    }

    /**
     * Proses gaji satu karyawan
     */
    public function prosesGajiKaryawan(User $karyawan)
    {
        $gajiPerBulan = (float) ($karyawan->gaji ?? 0);
        
        // 1. Hitung gaji pokok (prorata berdasarkan hari kerja)
        $gajiPokok = $this->hitungGajiPokok($gajiPerBulan);
        
        // 2. Data ketidakhadiran
        $kehadiran = $this->hitungKehadiran($karyawan->id);
        
        // 3. Potongan tidak hadir
        $potonganTidakHadir = $this->hitungPotonganTidakHadir($gajiPerBulan, $kehadiran);
        
        // 4. Tunjangan tetap
        $tunjanganTetap = $this->hitungTunjanganTetap();
        
        // 5. Tunjangan kinerja dari KPA
        $kpa = $this->ambilNilaiKPA($karyawan->id);
        $tunjanganKinerja = $this->hitungTunjanganKinerja($gajiPokok, $kpa);
        
        // 6. Lembur
        $jamLembur = $kehadiran['jam_lembur'] ?? 0;
        $upahLembur = $this->hitungUpahLembur($jamLembur);
        
        // 7. Total gaji bersih
        $totalGaji = $gajiPokok - $potonganTidakHadir + $tunjanganTetap + $tunjanganKinerja + $upahLembur;
        
        // Simpan ke payroll_details
        return PayrollDetail::updateOrCreate(
            [
                'payroll_period_id' => $this->period->id,
                'user_id' => $karyawan->id,
            ],
            [
                'karyawan_id' => $karyawan->id,
                'nama_karyawan' => $karyawan->name,
                'divisi' => $karyawan->divisi ?? '-',
                'gaji_per_bulan' => $gajiPerBulan,
                'total_hari_kerja' => $this->totalHariKerja,
                'hari_hadir' => $kehadiran['hadir'] ?? 0,
                'hari_alfa' => $kehadiran['alfa'] ?? 0,
                'hari_sakit_tanpa_surat' => $kehadiran['sakit_tanpa_surat'] ?? 0,
                'hari_izin_tanpa_ket' => $kehadiran['izin_tanpa_ket'] ?? 0,
                'hari_cuti' => $kehadiran['cuti'] ?? 0,
                'jam_lembur' => $jamLembur,
                'gaji_pokok' => $gajiPokok,
                'potongan_tidak_hadir' => $potonganTidakHadir,
                'tunjangan_tetap' => $tunjanganTetap,
                'nilai_kpa' => $kpa,
                'persentase_tunjangan_kinerja' => $this->getPersentaseKPA($kpa),
                'tunjangan_kinerja' => $tunjanganKinerja,
                'tarif_lembur_per_jam' => PayrollAllowance::where('tipe', 'tarif_lembur')->where('is_active', 1)->first()?->nilai ?? 25000,
                'upah_lembur' => $upahLembur,
                'total_gaji_bersih' => max(0, $totalGaji),
                'status' => 'draft'
            ]
        );
    }

    /**
     * Hitung gaji pokok = (gaji per bulan / 30) × total hari kerja
     */
    protected function hitungGajiPokok($gajiPerBulan)
    {
        $gajiPerHari = $gajiPerBulan / 30;
        return round($gajiPerHari * $this->totalHariKerja, 2);
    }

    /**
     * Hitung kehadiran dan ketidakhadiran dari tabel absensis
     */
    protected function hitungKehadiran($userId)
    {
        $absensi = DB::table('absensis')
            ->where('user_id', $userId)
            ->whereBetween('tanggal', [$this->tanggalMulai, $this->tanggalSelesai])
            ->get();
        
        $result = [
            'hadir' => 0,
            'alfa' => 0,
            'sakit_tanpa_surat' => 0,
            'izin_tanpa_ket' => 0,
            'cuti' => 0,
            'jam_lembur' => 0,
        ];
        
        foreach ($absensi as $absen) {
            $jenis = $absen->jenis_ketidakhadiran;
            
            // Jika ada jam_masuk, dianggap hadir
            if ($absen->jam_masuk !== null) {
                $result['hadir']++;
            } 
            // Jika tidak ada jam_masuk, cek jenis ketidakhadirannya
            elseif ($jenis == 'cuti') {
                $result['cuti']++;
            } 
            elseif ($jenis == 'sakit') {
                $result['sakit_tanpa_surat']++;
            } 
            elseif ($jenis == 'izin') {
                $result['izin_tanpa_ket']++;
            } 
            else {
                $result['alfa']++;
            }
        }
        
        return $result;
    }

    /**
     * Potongan = (gaji per hari) × total hari tidak hadir (alfa + sakit tanpa surat + izin tanpa ket)
     */
    protected function hitungPotonganTidakHadir($gajiPerBulan, $kehadiran)
    {
        $totalTidakHadir = $kehadiran['alfa'] + $kehadiran['sakit_tanpa_surat'] + $kehadiran['izin_tanpa_ket'];
        $gajiPerHari = $gajiPerBulan / 30;
        return round($gajiPerHari * $totalTidakHadir, 2);
    }

    /**
     * Ambil total tunjangan tetap yang aktif
     */
    protected function hitungTunjanganTetap()
    {
        $tunjangan = PayrollAllowance::where('tipe', 'tunjangan_tetap')
            ->where('is_active', 1)
            ->sum('nilai');
        return (float) $tunjangan;
    }

    /**
     * Ambil nilai KPA karyawan untuk periode ini
     */
    protected function ambilNilaiKPA($userId)
    {
        $kpa = DB::table('kpa')
            ->where('karyawan_id', $userId)
            ->where('bulan', $this->period->bulan)
            ->where('tahun', $this->period->tahun)
            ->first();
        
        if ($kpa) {
            // Prioritaskan nilai_akhir, kalau tidak ada pakai nilai_rata_rata
            return (float) ($kpa->nilai_akhir ?? $kpa->nilai_rata_rata ?? 75);
        }
        
        // Default nilai 75 jika belum ada KPA
        return 75;
    }

    /**
     * Dapatkan persentase tunjangan berdasarkan nilai KPA
     */
    protected function getPersentaseKPA($nilaiKPA)
    {
        $rule = KpaTunjanganRule::where('nilai_min', '<=', $nilaiKPA)
            ->where('nilai_max', '>=', $nilaiKPA)
            ->where('is_active', 1)
            ->first();
        
        return $rule ? (float) $rule->persentase : 0;
    }

    /**
     * Hitung tunjangan kinerja = gaji pokok × persentase
     */
    protected function hitungTunjanganKinerja($gajiPokok, $nilaiKPA)
    {
        $persen = $this->getPersentaseKPA($nilaiKPA);
        return round($gajiPokok * ($persen / 100), 2);
    }

    /**
     * Hitung upah lembur = jam lembur × tarif lembur
     */
    protected function hitungUpahLembur($jamLembur)
    {
        $tarif = PayrollAllowance::where('tipe', 'tarif_lembur')
            ->where('is_active', 1)
            ->first()?->nilai ?? 25000;
        return round($jamLembur * $tarif, 2);
    }
}