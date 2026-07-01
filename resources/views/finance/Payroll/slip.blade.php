<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $period->nama_periode }}</title>
    <style>
        /* Pengaturan Kertas Cetak A4 */
        @page {
            size: A4 portrait;
            margin: 20px;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 11px;
            color: #334155;
            line-height: 1.5;
            background-color: #ffffff;
        }

        /* Container Header */
        .header-container {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 3px double #4f46e5;
            padding-bottom: 12px;
        }
        
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .header-table td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }
        
        .company-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .company-details {
            font-size: 10px;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.4;
        }
        
        .slip-title-container {
            text-align: right;
        }
        
        .slip-title-container h1 {
            color: #4f46e5;
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 1px;
        }
        
        .slip-period {
            font-size: 11px;
            color: #475569;
            font-weight: bold;
            margin-top: 4px;
        }

        /* Gaya Judul Bagian */
        .section-title {
            background: #f1f5f9;
            color: #1e293b;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-left: 4px solid #4f46e5;
        }

        /* Tabel Data Karyawan (Ringkas Horizontal) */
        .employee-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .employee-table td {
            padding: 4px 4px;
            border: none !important;
            vertical-align: top;
        }

        /* Master Grid Layout (Membagi Pendapatan & Potongan Kiri-Kanan) */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .grid-table > tr > td {
            border: none !important;
            padding: 0 !important;
            vertical-align: top;
        }

        /* Tabel Rincian Keuangan */
        .finance-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .finance-table th {
            background: #f8fafc;
            font-weight: bold;
            color: #475569;
            border-bottom: 2px solid #cbd5e1;
            font-size: 10px;
            text-transform: uppercase;
            padding: 6px 8px;
        }
        
        .finance-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        
        /* Baris Total Rincian */
        .total-row td {
            background: #f8fafc;
            font-weight: bold;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
            color: #1e293b;
        }

        /* Kartu Gaji Bersih (Take Home Pay) */
        .thp-card {
            width: 100%;
            border-collapse: collapse;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            margin-top: 5px;
            margin-bottom: 25px;
        }
        
        .thp-card td {
            padding: 10px 14px;
            font-size: 12px;
            color: #1e293b;
            border: none !important;
        }
        
        .thp-card .amount {
            color: #4f46e5;
            font-size: 16px;
            font-weight: bold;
        }

        /* Area Tanda Tangan */
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        
        .ttd-table td {
            border: none !important;
            text-align: center;
            vertical-align: top;
            padding: 10px;
            width: 33.33%;
        }
        
        .ttd-label {
            margin-bottom: 12px;
            color: #475569;
            font-size: 11px;
        }
        
        .signature-box {
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .ttd-name {
            font-weight: bold;
            color: #1e293b;
            margin-top: 8px;
            border-top: 1px solid #cbd5e1;
            display: inline-block;
            padding-top: 3px;
            min-width: 120px;
        }

        /* Format Utility */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier New', Courier, monospace; font-size: 12px; font-weight: bold; }

        /* Footer */
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px dashed #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <table class="header-table">
            <tr>
                <td>
                    <div class="company-title">PT digital kolaborasi Indonesia</div>
                    <div class="company-details">
                        Jl. Teknologi No. 123, Jakarta<br>
                        Email: finance@digital kolaborasi.id
                    </div>
                </td>
                <td class="slip-title-container">
                    <h1>SLIP GAJI</h1>
                    <div class="slip-period">Periode: {{ $period->nama_periode }}</div>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <div class="section-title">Data Karyawan</div>
        <table class="employee-table">
            <tr>
                <td width="14%"><strong>Nama Karyawan</strong></td>
                <td width="36%">: {{ $detail->user->name ?? '-' }}</td>
                <td width="12%"><strong>Divisi / Unit</strong></td>
                <td width="38%">: {{ $detail->divisi ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Rentang Tanggal</strong></td>
                <td>: {{ \Carbon\Carbon::parse($period->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($period->tanggal_selesai)->format('d/m/Y') }}</td>
                <td><strong>Status Berkas</strong></td>
                <td>: Dokumen Elektronik Sah</td>
            </tr>
        </table>
    </div>
    
    <table class="grid-table">
        <tr>
            <td width="48%">
                <div class="section-title">Rincian Pendapatan</div>
                <table class="finance-table">
                    <thead>
                        <tr>
                            <th width="55%">Deskripsi Komponen</th>
                            <th width="45%" class="text-right">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Gaji Pokok</td><td class="text-right font-mono">Rp {{ number_format($detail->gaji_pokok, 0, ',', '.') }}</td></tr>
                        <tr><td>Tunjangan Tetap</td><td class="text-right font-mono">Rp {{ number_format($detail->tunjangan_tetap ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><td>Tunjangan Kinerja (KPA)</td><td class="text-right font-mono">Rp {{ number_format($detail->tunjangan_kinerja ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><td>Bonus</td><td class="text-right font-mono">Rp {{ number_format($detail->bonus ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><td><strong>Upah Lembur</strong></td><td class="text-right font-mono">Rp {{ number_format($detail->upah_lembur ?? 0, 0, ',', '.') }}</td></tr>
                        <tr class="total-row">
                            <td><strong>Total Pendapatan</strong></td>
                            <td class="text-right font-mono">Rp {{ number_format(($detail->gaji_pokok ?? 0) + ($detail->tunjangan_tetap ?? 0) + ($detail->tunjangan_kinerja ?? 0) + ($detail->bonus ?? 0) + ($detail->upah_lembur ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            
            <td width="4%"></td>
            
            <td width="48%">
                <div class="section-title">Rincian Potongan</div>
                <table class="finance-table">
                    <thead>
                        <tr>
                            <th width="55%">Deskripsi Komponen</th>
                            <th width="45%" class="text-right">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Potongan Tidak Hadir</td><td class="text-right font-mono">Rp {{ number_format($detail->potongan_tidak_hadir ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><td>Potongan BPJS</td><td class="text-right font-mono">Rp {{ number_format($detail->potongan_bpjs ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr class="total-row">
                            <td><strong>Total Potongan</strong></td>
                            <td class="text-right font-mono" style="color: #dc2626;">Rp {{ number_format(($detail->potongan_tidak_hadir ?? 0) + ($detail->potongan_bpjs ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    
    <table class="thp-card">
        <tr>
            <td><strong>TOTAL GAJI BERSIH (TAKE HOME PAY)</strong></td>
            <td class="text-right amount font-mono">Rp {{ number_format($detail->total_gaji_bersih, 0, ',', '.') }}</td>
        </tr>
    </table>
    
    <table class="ttd-table">
        <tr>
            <td>
                <div class="ttd-label">Hormat Kami,</div>
                <div class="signature-box">
                    <svg width="130" height="45" viewBox="0 0 200 40">
                        <path d="M10,25 Q40,10 70,25 T130,25 T190,20" fill="none" stroke="#4f46e5" stroke-width="1.8"/>
                        <text x="10" y="38" font-size="8" fill="#94a3b8" letter-spacing="1">DIGITAL FINANCE SIGN</text>
                    </svg>
                </div>
                <div class="ttd-name">Finance Team</div>
            </td>
            <td>
                <div class="ttd-label">Mengetahui,</div>
                <div class="signature-box">
                    <svg width="130" height="45" viewBox="0 0 200 40">
                        <path d="M15,20 Q50,30 85,15 T155,20 T185,25" fill="none" stroke="#334155" stroke-width="1.8"/>
                        <text x="15" y="38" font-size="8" fill="#94a3b8" letter-spacing="1">HR MANAGEMENT APP</text>
                    </svg>
                </div>
                <div class="ttd-name">HR Manager</div>
            </td>
            <td>
                <div class="ttd-label">Penerima,</div>
                <div class="signature-box">
                    <svg width="130" height="45" viewBox="0 0 200 40">
                        <path d="M20,22 Q60,15 100,25 T180,20" fill="none" stroke="#334155" stroke-width="1.5"/>
                        <text x="20" y="38" font-size="8" fill="#94a3b8" letter-spacing="0.5">EMPLOYEE ID VERIFIED</text>
                    </svg>
                </div>
                <div class="ttd-name">{{ substr($detail->user->name ?? 'Karyawan', 0, 20) }}</div>
            </td>
        </tr>
    </table>
    
    <div class="footer">
        Dokumen ini dibuat, dihitung, dan diterbitkan secara otomatis oleh Sistem Payroll PT digital kolaborasi Indonesia.<br>
        Dicetak pada berkas digital resmi karyawan. &copy; {{ date('Y') }} PT digital kolaborasi Indonesia. All Rights Reserved.
    </div>
</body>
</html>
