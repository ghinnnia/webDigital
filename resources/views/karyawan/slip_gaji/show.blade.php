<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $slip->user->name ?? 'Karyawan' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
        }
        
        /* Pengaturan Cetak Browser */
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            body * { 
                visibility: hidden; 
            }
            #slipGajiContainer, #slipGajiContainer * { 
                visibility: visible; 
            }
            #slipGajiContainer { 
                position: absolute; 
                top: 0; 
                left: 0; 
                width: 100%; 
                margin: 0; 
                padding: 0;
                box-shadow: none;
                border: none;
                border-radius: 0;
            }
            .no-print { 
                display: none !important; 
            }
            
            /* Menyesuaikan style cetak bawaan dari template finance */
            .slip-box {
                font-size: 11px !important;
                color: #334155 !important;
            }
            .header-container {
                border-bottom: 3px double #4f46e5 !important;
            }
            .section-title {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                border-left: 4px solid #4f46e5 !important;
            }
            .finance-table th {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .total-row td {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .thp-card {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .thp-card .amount {
                color: #4f46e5 !important;
            }
        }

        /* Gaya Khusus Replika Tampilan Cetak */
        .slip-box {
            font-size: 11px;
            color: #334155;
            line-height: 1.5;
        }
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
        .total-row td {
            background: #f8fafc;
            font-weight: bold;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
            color: #1e293b;
        }
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
        .ttd-image {
            max-width: 130px;
            max-height: 50px;
            object-fit: contain;
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
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier New', Courier, monospace; font-size: 12px; font-weight: bold; }
        .footer-slip {
            margin-top: 25px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px dashed #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body class="bg-gray-100">
    @include('karyawan.templet.header')

    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-4xl mx-auto">
            <!-- Tombol Kembali Langsung ke Halaman Notifikasi -->
            <div class="mb-4 no-print">
                <a href="/notifications" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-1 font-medium">
                    ← Kembali ke Notifikasi
                </a>
            </div>

            <!-- Card Utama / Container Slip -->
            <div id="slipGajiContainer" class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden p-8 slip-box">
                
                @php
                    // Ambil data TTD dari database
                    $ttdFinance = \App\Models\TtdSetting::where('role', 'finance')->where('is_active', true)->first();
                    $ttdHR = \App\Models\TtdSetting::where('role', 'hr')->where('is_active', true)->first();
                    $ttdKaryawan = \App\Models\TtdSetting::where('role', 'karyawan')->where('is_active', true)->first();
                @endphp

                <!-- Header Kontainer -->
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
                            <td class="slip-title-container">
                                <h1>SLIP GAJI</h1>
                                <div class="slip-period">Periode: {{ $slip->payrollPeriod->nama_periode ?? '-' }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Data Karyawan -->
                <div class="section">
                    <div class="section-title">Data Karyawan</div>
                    <table class="employee-table">
                        <tr>
                            <td width="14%"><strong>Nama Karyawan</strong></td>
                            <td width="36%">: {{ $slip->user->name ?? '-' }}</td>
                            <td width="12%"><strong>Divisi / Unit</strong></td>
                            <td width="38%">: {{ $slip->divisi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Rentang Tanggal</strong></td>
                            <td>: 
                                @if(isset($slip->payrollPeriod))
                                    {{ \Carbon\Carbon::parse($slip->payrollPeriod->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($slip->payrollPeriod->tanggal_selesai)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td><strong>Status Berkas</strong></td>
                            <td>: Dokumen Elektronik Sah</td>
                        </tr>
                    </table>
                </div>
                
                <!-- Master Grid Pendapatan & Potongan -->
                <table class="grid-table">
                    <tr>
                        <!-- Sisi Kiri: Pendapatan -->
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
                                    <tr><td>Gaji Pokok</td><td class="text-right font-mono">Rp {{ number_format($slip->gaji_pokok, 0, ',', '.') }}</td></tr>
                                    <tr><td>Tunjangan Tetap</td><td class="text-right font-mono">Rp {{ number_format($slip->tunjangan_tetap ?? 0, 0, ',', '.') }}</td></tr>
                                    <tr><td>Tunjangan Kinerja (KPA)</td><td class="text-right font-mono">Rp {{ number_format($slip->tunjangan_kinerja ?? 0, 0, ',', '.') }}</td></tr>
                                    <tr><td>Bonus</td><td class="text-right font-mono">Rp {{ number_format($slip->bonus ?? 0, 0, ',', '.') }}</td></tr>
                                    <tr><td><strong>Upah Lembur</strong></td><td class="text-right font-mono">Rp {{ number_format($slip->upah_lembur ?? 0, 0, ',', '.') }}</td></tr>
                                    <tr class="total-row">
                                        <td><strong>Total Pendapatan</strong></td>
                                        <td class="text-right font-mono">Rp {{ number_format(($slip->gaji_pokok ?? 0) + ($slip->tunjangan_tetap ?? 0) + ($slip->tunjangan_kinerja ?? 0) + ($slip->bonus ?? 0) + ($slip->upah_lembur ?? 0), 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        
                        <!-- Jarak Antar Kolom -->
                        <td width="4%"></td>
                        
                        <!-- Sisi Kanan: Potongan -->
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
                                    <tr><td>Potongan Tidak Hadir</td><td class="text-right font-mono">Rp {{ number_format($slip->potongan_tidak_hadir ?? 0, 0, ',', '.') }}</td></tr>
                                    <tr><td>Potongan BPJS</td><td class="text-right font-mono">Rp {{ number_format($slip->potongan_bpjs ?? 0, 0, ',', '.') }}</td></tr>
                                    <tr class="total-row">
                                        <td><strong>Total Potongan</strong></td>
                                        <td class="text-right font-mono" style="color: #dc2626;">Rp {{ number_format(($slip->potongan_tidak_hadir ?? 0) + ($slip->potongan_bpjs ?? 0), 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
                
                <!-- Total Gaji Bersih / THP -->
                <table class="thp-card">
                    <tr>
                        <td><strong>TOTAL GAJI BERSIH (TAKE HOME PAY)</strong></td>
                        <td class="text-right amount font-mono">Rp {{ number_format($slip->total_gaji_bersih, 0, ',', '.') }}</td>
                    </tr>
                </table>
                
                <!-- Area Tanda Tangan Digital Bermodelkan Dokumen Dinas -->
                <table class="ttd-table">
                    <tr>
                        <!-- Finance -->
                        <td>
                            <div class="ttd-label">Hormat Kami,</div>
                            <div class="signature-box">
                                @if($ttdFinance && $ttdFinance->file_path)
                                    <img src="{{ asset('storage/' . $ttdFinance->file_path) }}" class="ttd-image" alt="TTD Finance">
                                @else
                                    <svg width="130" height="45" viewBox="0 0 200 40">
                                        <path d="M10,25 Q40,10 70,25 T130,25 T190,20" fill="none" stroke="#4f46e5" stroke-width="1.8"/>
                                        <text x="10" y="38" font-size="8" fill="#94a3b8" letter-spacing="1">DIGITAL FINANCE SIGN</text>
                                    </svg>
                                @endif
                            </div>
                            <div class="ttd-name">{{ $ttdFinance->nama_pejabat ?? 'Finance Team' }}</div>
                            <div style="font-size: 9px; color: #64748b;">{{ $ttdFinance->jabatan ?? 'Finance Staff' }}</div>
                        </td>
                        
                        <!-- HR -->
                        <td>
                            <div class="ttd-label">Mengetahui,</div>
                            <div class="signature-box">
                                @if($ttdHR && $ttdHR->file_path)
                                    <img src="{{ asset('storage/' . $ttdHR->file_path) }}" class="ttd-image" alt="TTD HR">
                                @else
                                    <svg width="130" height="45" viewBox="0 0 200 40">
                                        <path d="M15,20 Q50,30 85,15 T155,20 T185,25" fill="none" stroke="#334155" stroke-width="1.8"/>
                                        <text x="15" y="38" font-size="8" fill="#94a3b8" letter-spacing="1">HR MANAGEMENT APP</text>
                                    </svg>
                                @endif
                            </div>
                            <div class="ttd-name">{{ $ttdHR->nama_pejabat ?? 'HR Manager' }}</div>
                            <div style="font-size: 9px; color: #64748b;">{{ $ttdHR->jabatan ?? 'HR Manager' }}</div>
                        </td>
                        
                        <!-- Karyawan (Penerima) -->
                        <td>
                            <div class="ttd-label">Penerima,</div>
                            <div class="signature-box">
                                @if($ttdKaryawan && $ttdKaryawan->file_path)
                                    <img src="{{ asset('storage/' . $ttdKaryawan->file_path) }}" class="ttd-image" alt="TTD Karyawan">
                                @else
                                    <svg width="130" height="45" viewBox="0 0 200 40">
                                        <path d="M20,22 Q60,15 100,25 T180,20" fill="none" stroke="#334155" stroke-width="1.5"/>
                                        <text x="20" y="38" font-size="8" fill="#94a3b8" letter-spacing="0.5">EMPLOYEE ID VERIFIED</text>
                                    </svg>
                                @endif
                            </div>
                            <div class="ttd-name">{{ $slip->user->name ?? 'Karyawan' }}</div>
                            <div style="font-size: 9px; color: #64748b;">Karyawan Bersangkutan</div>
                        </td>
                    </tr>
                </table>
                
                <!-- Footer Terlampir Di Dalam Berkas -->
                <div class="footer-slip">
                    Dokumen ini dibuat, dihitung, dan diterbitkan secara otomatis oleh Sistem Payroll PT Digicity Indonesia.<br>
                    Dicetak pada berkas digital resmi karyawan. &copy; {{ date('Y') }} PT Digicity Indonesia. All Rights Reserved.
                </div>
            </div>

            <!-- Tombol Aksi Web (Sembunyi ketika cetak) -->
            <div class="flex justify-end gap-3 mt-6 no-print">
                <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 shadow-md transition">
                    🖨️ Cetak Slip Gaji
                </button>
                <a href="/notifications" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</body>
</html>