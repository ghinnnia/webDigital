<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->nomor_order }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: #fff;
            padding: 0;
        }

        .invoice-container {
            width: 210mm;
            height: 297mm;
            margin: auto;
            padding: 20mm;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1f2937;
        }

        .company-logo {
            flex: 1;
        }

        .company-logo h1 {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .company-info {
            font-size: 11px;
            line-height: 1.6;
            color: #666;
        }

        .company-info p {
            margin: 2px 0;
        }

        .invoice-header {
            flex: 1;
            text-align: right;
        }

        .invoice-header h2 {
            font-size: 32px;
            color: #1f2937;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .invoice-details {
            font-size: 12px;
            line-height: 1.8;
            color: #555;
        }

        .invoice-details p {
            margin: 5px 0;
        }

        .invoice-no {
            font-weight: 600;
            color: #1f2937;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #1f2937;
            margin-top: 20px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .client-info {
            margin-bottom: 25px;
            padding: 12px;
            background: #f8f9fa;
            border-left: 4px solid #3b82f6;
            border-radius: 2px;
        }

        .client-info p {
            font-size: 12px;
            margin: 4px 0;
            line-height: 1.5;
        }

        .client-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 13px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table thead {
            background: #1f2937;
            color: white;
        }

        table thead th {
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #1f2937;
        }

        table tbody td {
            padding: 12px 8px;
            font-size: 12px;
            border: 1px solid #ddd;
            line-height: 1.5;
        }

        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        table tbody tr:hover {
            background: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-section {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        .summary-table {
            width: 350px;
        }

        .summary-table table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 10px 12px;
            font-size: 12px;
            border: none;
        }

        .summary-label {
            text-align: right;
            font-weight: 500;
            width: 60%;
            color: #555;
        }

        .summary-value {
            text-align: right;
            width: 40%;
            font-weight: 500;
            color: #1f2937;
        }

        .summary-row {
            border-bottom: 1px solid #ddd;
        }

        .summary-row.subtotal {
            border-bottom: 2px solid #ddd;
        }

        .summary-row.total {
            border-top: 2px solid #1f2937;
            border-bottom: 2px solid #1f2937;
            background: #f8f9fa;
            font-size: 14px;
            font-weight: 700;
        }

        .payment-section {
            margin-top: 25px;
            padding: 12px;
            background: #e3f2fd;
            border-left: 4px solid #3b82f6;
            border-radius: 2px;
        }

        .payment-section p {
            font-size: 12px;
            margin: 4px 0;
            line-height: 1.5;
        }

        .payment-method {
            font-weight: 600;
            color: #1f2937;
            font-size: 13px;
        }

        .notes-section {
            margin-top: 25px;
            padding: 12px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 2px;
        }

        .notes-section p {
            font-size: 11px;
            margin: 3px 0;
            line-height: 1.4;
            color: #666;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
            line-height: 1.6;
        }

        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            padding-top: 20px;
        }

        .signature-box {
            width: 40%;
            text-align: center;
            font-size: 11px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin: 50px 0 5px 0;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 20mm;
                width: 100%;
                height: 100%;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

<div class="invoice-container">

    <!-- HEADER -->
    <div class="header">
        <div class="company-logo">
            <h1>DIGICITY</h1>
            <div class="company-info">
                <p><strong>PT. Digicity Indonesia</strong></p>
                <p>Jln. Raya Gatot Subroto No. 123</p>
                <p>Jakarta Selatan 12930</p>
                <p>Telp: +62 21 1234 5678</p>
                <p>Email: invoice@digicity.id</p>
            </div>
        </div>

        <div class="invoice-header">
            <h2>INVOICE</h2>
            <div class="invoice-details">
                <p><span class="invoice-no">No. Invoice:</span> {{ $invoice->nomor_order }}</p>
                <p>Tanggal: {{ now()->format('d M Y') }}</p>
                <p>Jatuh Tempo: {{ now()->addDays(30)->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    <!-- BILL TO / CLIENT INFO -->
    <div style="margin-bottom: 20px;">
        <p class="section-title">Tagihan Kepada:</p>
        <div class="client-info">
            <p class="client-name">{{ $invoice->nama_klien }}</p>
            <p><strong>Perusahaan:</strong> {{ $invoice->nama_perusahaan ?? 'PT. Klien' }}</p>
            <p><strong>Alamat:</strong> {{ $invoice->alamat ?? 'Alamat klien' }}</p>
        </div>
    </div>

    <!-- ITEMS TABLE -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 50%;">Deskripsi Layanan</th>
                <th style="width: 15%; text-align: right;">Harga Unit</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 20%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>{{ $invoice->detail_layanan }}</td>
                <td class="text-right">Rp {{ number_format($invoice->harga, 0, ',', '.') }}</td>
                <td class="text-center">1</td>
                <td class="text-right">Rp {{ number_format($invoice->harga, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- SUMMARY -->
    <div class="summary-section">
        <div class="summary-table">
            <table>
                <tr class="summary-row">
                    <td class="summary-label">Subtotal</td>
                    <td class="summary-value">Rp {{ number_format($invoice->harga, 0, ',', '.') }}</td>
                </tr>
                <tr class="summary-row">
                    <td class="summary-label">Pajak ({{ $invoice->pajak ?? 0 }}%)</td>
                    <td class="summary-value">Rp {{ number_format(($invoice->pajak ?? 0) * $invoice->harga / 100, 0, ',', '.') }}</td>
                </tr>
                <tr class="summary-row subtotal">
                    <td class="summary-label">Diskon</td>
                    <td class="summary-value">Rp 0</td>
                </tr>
                <tr class="summary-row total">
                    <td class="summary-label">TOTAL</td>
                    <td class="summary-value">Rp {{ number_format($invoice->harga + (($invoice->pajak ?? 0) * $invoice->harga / 100), 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- PAYMENT METHOD -->
    <div class="payment-section">
        <p class="payment-method">Metode Pembayaran</p>
        <p>{{ $invoice->metode_pembayaran ?? 'Transfer Bank' }}</p>
        <p style="margin-top: 8px; font-size: 10px; color: #666;">
            Mohon lakukan pembayaran dalam 30 hari sejak tanggal invoice.
        </p>
    </div>

    <!-- NOTES -->
    <div class="notes-section">
        <p><strong>Catatan:</strong></p>
        <p>Terima kasih atas kepercayaan Anda. Apabila ada pertanyaan mengenai invoice ini, silakan hubungi kami.</p>
    </div>

    <!-- SIGNATURE SECTION -->
    <div class="signature-section">
        <div class="signature-box">
            <p style="margin-bottom: 5px; font-size: 11px; color: #666;">Dibuat oleh:</p>
            <div class="signature-line"></div>
            <p>(_________________)</p>
            <p style="font-size: 10px; color: #666; margin-top: 3px;">Finance Department</p>
        </div>
        <div class="signature-box">
            <p style="margin-bottom: 5px; font-size: 11px; color: #666;">Disetujui oleh:</p>
            <div class="signature-line"></div>
            <p>(_________________)</p>
            <p style="font-size: 10px; color: #666; margin-top: 3px;">Manager</p>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>Dokumen ini adalah bukti transaksi yang sah. Terima kasih telah berbisnis dengan kami.</p>
        <p>© 2025 PT. Digicity Indonesia. All rights reserved.</p>
    </div>

</div>

</body>
</html>
