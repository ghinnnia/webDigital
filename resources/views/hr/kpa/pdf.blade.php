<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan KPA - {{ $nama_bulan }} {{ $tahun }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 9px;
        }
        .text-left {
            text-align: left;
        }
        .grade-A { background-color: #d1fae5; color: #065f46; }
        .grade-B { background-color: #dbeafe; color: #1e40af; }
        .grade-C { background-color: #fef3c7; color: #92400e; }
        .grade-D { background-color: #fee2e2; color: #991b1b; }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KINERJA PEGAWAI (KPA)</h1>
        <p>Periode: {{ $nama_bulan }} {{ $tahun }} | Tanggal Cetak: {{ $tanggal_cetak }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Karyawan</th>
                <th rowspan="2">Divisi</th>
                <th colspan="4">PERFORMANCE</th>
                <th colspan="7">KOMPETENSI</th>
                <th colspan="3">SIKAP</th>
                <th rowspan="2">Total</th>
                <th rowspan="2">Grade</th>
            </tr>
            <tr>
                <th>Target</th><th>Kualitas</th><th>Ketepatan</th><th>Kuantitas</th>
                <th>Kerja Sama</th><th>Inisiatif</th><th>Kemandirian</th><th>Disiplin</th>
                <th>Tanggung Jawab</th><th>Kompetensi Teknik</th><th>Kepemimpinan</th>
                <th>Pelayanan</th><th>Integrasi</th><th>Adaptasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kinerjaList as $index => $item)
            @php
                $karyawan = $item['karyawan'] ?? null;
                $karyawanName = $karyawan->name ?? '-';
                $karyawanDivisi = $karyawan->divisi->divisi ?? '-';
                $penilaian = $item['penilaian'] ?? [];
                $totalNilai = $item['total_nilai'] ?? 0;
                $grade = $item['grade'] ?? 'D';
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $karyawanName }}</td>
                <td>{{ $karyawanDivisi }}</td>
                
                <!-- PERFORMANCE -->
                <td>{{ $penilaian['target_tugas'] ?? 0 }}%</td>
                <td>{{ $penilaian['kualitas_kerja'] ?? 0 }}%</td>
                <td>{{ $penilaian['ketepatan_waktu'] ?? 0 }}%</td>
                <td>{{ $penilaian['kuantitas_kerja'] ?? 0 }}%</td>
                
                <!-- KOMPETENSI -->
                <td>{{ $penilaian['kerja_sama_tim'] ?? 0 }}%</td>
                <td>{{ $penilaian['inisiatif'] ?? 0 }}%</td>
                <td>{{ $penilaian['kemandirian'] ?? 0 }}%</td>
                <td>{{ $penilaian['disiplin'] ?? 0 }}%</td>
                <td>{{ $penilaian['tanggung_jawab'] ?? 0 }}%</td>
                <td>{{ $penilaian['kompetensi_teknik'] ?? 0 }}%</td>
                <td>{{ $penilaian['kepemimpinan'] ?? 0 }}%</td>
                
                <!-- SIKAP -->
                <td>{{ $penilaian['orientasi_pelayanan'] ?? 0 }}%</td>
                <td>{{ $penilaian['integrasi_komitmen'] ?? 0 }}%</td>
                <td>{{ $penilaian['adaptasi'] ?? 0 }}%</td>
                
                <td><strong>{{ $totalNilai }}%</strong></td>
                <td class="grade-{{ $grade }}">{{ $grade }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="21" style="text-align: center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->name ?? 'System' }}</p>
        <p>* Laporan ini dihasilkan secara otomatis oleh sistem</p>
    </div>
</body>
</html>