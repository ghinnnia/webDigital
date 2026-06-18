<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $period->nama_periode }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { color: #4f46e5; margin: 0; font-size: 20px; }
        .company-info { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 20px; }
        .section-title { background: #f1f5f9; padding: 6px; font-weight: bold; font-size: 13px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #f1f5f9; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .ttd-table { width: 100%; margin-top: 30px; }
        .ttd-table td { border: none; text-align: center; vertical-align: bottom; padding: 20px 10px; }
        .ttd-image { max-width: 120px; max-height: 50px; }
    </style>
</head>
<body>
   @php
    // Ambil data TTD dari database
    $ttdFinance = \App\Models\TtdSetting::where('role', 'finance')->where('is_active', true)->first();
    $ttdHR = \App\Models\TtdSetting::where('role', 'hr')->where('is_active', true)->first();
    $ttdKaryawan = \App\Models\TtdSetting::where('role', 'karyawan')->where('is_active', true)->first();
    
    // Helper function untuk mendapatkan path gambar
    function getTtdImagePath($ttd)
    {
        if (!$ttd || !$ttd->file_path) return null;
        
        // Cek beberapa kemungkinan path
        $paths = [
            public_path('storage/' . $ttd->file_path),
            storage_path('app/public/' . $ttd->file_path),
            base_path('storage/app/public/' . $ttd->file_path),
        ];
        
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        return null;
    }
    
    $financeTtdPath = getTtdImagePath($ttdFinance);
    $hrTtdPath = getTtdImagePath($ttdHR);
    $karyawanTtdPath = getTtdImagePath($ttdKaryawan);
@endphp

<!-- Tanda Tangan -->
<table class="ttd-table">
    <tr>
        <td class="text-center">
            <p>Hormat Kami,</p>
            <div class="signature-box">
                @if($financeTtdPath && file_exists($financeTtdPath))
                    <img src="{{ $financeTtdPath }}" style="max-width: 120px; max-height: 50px;" alt="TTD Finance">
                @else
                    <div style="height: 40px;">_________________</div>
                @endif
            </div>
            <p><strong>{{ $ttdFinance->nama_pejabat ?? 'Finance' }}</strong></p>
            <p style="font-size: 10px;">{{ $ttdFinance->jabatan ?? 'Finance Staff' }}</p>
        </td>
        <td class="text-center">
            <p>Mengetahui,</p>
            <div class="signature-box">
                @if($hrTtdPath && file_exists($hrTtdPath))
                    <img src="{{ $hrTtdPath }}" style="max-width: 120px; max-height: 50px;" alt="TTD HR">
                @else
                    <div style="height: 40px;">_________________</div>
                @endif
            </div>
            <p><strong>{{ $ttdHR->nama_pejabat ?? 'HR Manager' }}</strong></p>
            <p style="font-size: 10px;">{{ $ttdHR->jabatan ?? 'HR Manager' }}</p>
        </td>
        <td class="text-center">
            <p>Penerima,</p>
            <div class="signature-box">
                @if($karyawanTtdPath && file_exists($karyawanTtdPath))
                    <img src="{{ $karyawanTtdPath }}" style="max-width: 120px; max-height: 50px;" alt="TTD Karyawan">
                @else
                    <div style="height: 40px;">_________________</div>
                @endif
            </div>
            <p><strong>{{ $detail->user->name ?? 'Karyawan' }}</strong></p>
        </td>
    </tr>
</table>
    
    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh sistem.<br>
        &copy; {{ date('Y') }} Digicity Indonesia
    </div>
</body>
</html>