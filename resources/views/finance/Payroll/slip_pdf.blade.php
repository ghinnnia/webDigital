<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $period->nama_periode }}</title>
    <style>
        @page {
            size: A4;
            margin: 20px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 15px;
            font-size: 11px;
            color: #334155;
            line-height: 1.5;
        }
        
        /* Header & Company Styling */
        .header-container {
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 3px double #4f46e5;
            padding-bottom: 12px;
        }
        .header-table td {
            border: none !important;
            padding: 0 !important;
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
            margin-top: 2px;
        }
        .slip-title {
            text-align: right;
        }
        .slip-title h1 {
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
            margin-top: 3px;
        }

        /* Section Title Styling */
        .section-title {
            background: #e2e8f0;
            color: #1e293b;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-left: 4px solid #4f46e5;
        }

        /* Table Master Grid (Dua Kolom Berdampingan) */
        .grid-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .grid-table >  tr > td {
            border: none !important;
            padding: 0 !important;
            vertical-align: top;
        }

        /* General Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 5px 8px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
        }
        
        /* Employee Data Table Specific */
        .employee-table td {
            padding: 4px 0px;
            border: none;
        }

        /* Financial Table Specific */
        .finance-table th {
            background: #f8fafc;
            font-weight: bold;
            color: #475569;
            border-bottom: 2px solid #cbd5e1;
            font-size: 10px;
            text-transform: uppercase;
        }
        .finance-table td {
            color: #334155;
        }
        
        /* Utilities */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; font-size: 12px; }
        
        /* Total Row Highlights */
        .total-row td {
            background: #f8fafc;
            font-weight: bold;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
            color: #1e293b;
        }
        .take-home-pay-card {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            margin-top: 5px;
            margin-bottom: 25px;
        }
        .take-home-pay-card td {
            padding: 10px 12px;
            font-size: 13px;
            color: #1e293b;
            border: none !important;
        }
        .take-home-pay-card .amount {
            color: #4f46e5;
            font-size: 16px;
            font-weight: bold;
        }

        /* Signature Row */
        .ttd-table {
            width: 100%;
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
            margin-bottom: 45px;
            color: #475569;
        }
        .ttd-name {
            font-weight: bold;
            color: #1e293b;
            text-decoration: underline;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px dashed #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <table class="header-table">
            <tr>
                <td>
                    <div class="company-title">PT Digicity Indonesia</div>
                    <div class="company-details">
                        Jl. Teknologi No. 123, Jakarta<br>
                        Email: finance@digicity.id
                    </div>
                </td>
                <td class="slip-title">
                    <h1>SLIP GAJI</h1>
                    <div class="slip-period">Periode: {{ $period->nama_periode }}</div>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="section" style="margin-bottom: 15px;">
        <div class="section-title">Data Karyawan</div>
        <table class="employee-table">
            <tr>
                <td width="12%"><strong>Nama</strong></td>
                <td width="38%">: {{ $detail->user->name ?? '-' }}</td>
                <td width="12%"><strong>Divisi</strong></td>
                <td width="38%">: {{ $detail->divisi ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Rentang Tanggal</strong></td>
                <td>: {{ \Carbon\Carbon::parse($period->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($period->tanggal_selesai)->format('d/m/Y') }}</td>
                <td><strong>Status Cetak</strong></td>
                <td>: Dokumen Digital Asli</td>
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
                            <th>Deskripsi</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Gaji Pokok</td><td class="text-right font-mono">Rp {{ number_format($detail->gaji_pokok, 0, ',', '.') }}</td></tr>
                        <tr><td>Tunjangan Tetap</td><td class="text-right font-mono">Rp {{ number_format($detail->tunjangan_tetap ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><td>Tunjangan Kinerja (KPA)</td><td class="text-right font-mono">Rp {{ number_format($detail->tunjangan_kinerja ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><td>Bonus</td><td class="text-right font-mono">Rp {{ number_format($detail->bonus ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><td><strong>Upah Lembur</strong></td><td class="text-right font-mono"><strong>Rp {{ number_format($detail->upah_lembur ?? 0, 0, ',', '.') }}</strong></td></tr>
                        <tr class="total-row">
                            <td>Total Pendapatan</td>
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
                            <th>Deskripsi</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Potongan Tidak Hadir</td><td class="text-right font-mono">Rp {{ number_format($detail->potongan_tidak_hadir ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><td>Potongan BPJS</td><td class="text-right font-mono">Rp {{ number_format($detail->potongan_bpjs ?? 0, 0, ',', '.') }}</td></tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr class="total-row">
                            <td>Total Potongan</td>
                            <td class="text-right font-mono" style="color: #b91c1c;">Rp {{ number_format(($detail->potongan_tidak_hadir ?? 0) + ($detail->potongan_bpjs ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    
    <table class="take-home-pay-card">
        <tr>
            <td><strong>TOTAL GAJI BERSIH (TAKE HOME PAY)</strong></td>
            <td class="text-right amount font-mono">Rp {{ number_format($detail->total_gaji_bersih, 0, ',', '.') }}</td>
        </tr>
    </table>
    
    <table class="ttd-table">
        <tr>
            <td>
                <div class="ttd-label">Hormat Kami,</div>
                <div style="margin-bottom: 10px;">
                    <svg width="120" height="40" viewBox="0 0 300 60">
                        <path d="M20,40 C40,25 60,45 80,35 C100,25 120,40 140,30 C160,20 180,35 200,28 C220,21 240,30 260,25" stroke="#334155" stroke-width="2" fill="none"/>
                        <text x="10" y="55" font-size="9" fill="#94a3b8">Digitally Signed</text>
                    </svg>
                </div>
                <div class="ttd-name">Finance Team</div>
            </td>
            <td>
                <div class="ttd-label">Mengetahui,</div>
                <div style="margin-bottom: 10px;">
                    <svg width="120" height="40" viewBox="0 0 300 60">
                        <path d="M20,35 C45,25 65,45 90,30 C115,15 135,35 160,25 C185,15 205,30 230,22 C255,14 275,25 290,20" stroke="#334155" stroke-width="2" fill="none"/>
                        <text x="10" y="55" font-size="9" fill="#94a3b8">Digitally Approved</text>
                    </svg>
                </div>
                <div class="ttd-name">HR Manager</div>
            </td>
            <td>
                <div class="ttd-label">Penerima,</div>
                <div style="margin-bottom: 10px;">
                    <svg width="120" height="40" viewBox="0 0 300 60">
                        <path d="M20,38 C50,28 70,48 100,33 C130,18 150,38 180,28 C210,18 230,33 260,25" stroke="#334155" stroke-width="1.8" fill="none"/>
                        <text x="10" y="55" font-size="9" fill="#94a3b8">Sistem Slip Gaji</text>
                    </svg>
                </div>
                <div class="ttd-name">{{ $detail->user->name ?? 'Karyawan' }}</div>
            </td>
        </tr>
    </table>
    
    <div class="footer">
        Dokumen ini dibuat dan dicetak secara otomatis oleh sistem payroll PT Digicity Indonesia.<br>
        Segala bentuk modifikasi fisik tanpa persetujuan manajemen tidak sah. &copy; {{ date('Y') }} Digicity Indonesia
    </div>
</body>
</html>