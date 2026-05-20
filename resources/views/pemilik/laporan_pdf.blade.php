<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Laporan Keuangan Owner</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #111827; font-size: 12px; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        .meta { margin: 0 0 12px; color: #4b5563; }
        .summary { margin: 8px 0 14px; }
        .summary-box { display: inline-block; margin-right: 12px; padding: 8px 10px; border: 1px solid #e5e7eb; border-radius: 6px; }
        .label { color: #6b7280; font-size: 11px; }
        .value { font-weight: bold; font-size: 13px; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <h1>Laporan Keuangan Owner</h1>
    <p class="meta">Dicetak: {{ $generatedAt }}</p>
    <p class="meta">
        Filter:
        <span class="muted">
            Tipe: {{ $filterType === 'all' ? 'Semua' : ucfirst($filterType) }}
            @if(!empty($filterKategori))
                | Kategori: {{ implode(', ', $filterKategori) }}
            @endif
            @if(!empty($searchTerm))
                | Pencarian: "{{ $searchTerm }}"
            @endif
        </span>
    </p>

    <div class="summary">
        <div class="summary-box">
            <div class="label">Total Pemasukan</div>
            <div class="value">Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Pengeluaran</div>
            <div class="value">Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Keuntungan</div>
            <div class="value">Rp {{ number_format($totalKeuntungan ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 36px;">No</th>
                <th style="width: 90px;">Tanggal</th>
                <th style="width: 120px;">Kategori</th>
                <th>Deskripsi</th>
                <th style="width: 90px;">Tipe</th>
                <th style="width: 110px;" class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($financeData as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['tanggal_transaksi'] ?? '-' }}</td>
                    <td>{{ $item['kategori'] ?? '-' }}</td>
                    <td>{{ $item['deskripsi'] ?? '-' }}</td>
                    <td class="text-center">{{ $item['tipe_transaksi'] === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                    <td class="text-right">Rp {{ number_format($item['jumlah'] ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center muted">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
