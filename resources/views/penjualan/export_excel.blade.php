<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .no-border {
            border: none;
        }
    </style>
</head>
<body>
    <h1>Laporan Penjualan</h1>
    <div class="info-section">
        <p><strong>Tanggal Ekspor:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Kode Penjualan</th>
                <th>Nama Pembeli</th>
                <th>Tanggal Penjualan</th>
                <th>Nama Barang</th>
                <th class="text-center">Jumlah</th>
                <th class="text-right">Harga Jual</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $currentPenjualanId = null;
            @endphp
            @foreach($penjualan as $item)
                @foreach($item->details as $detail)
                    <tr>
                        @if($currentPenjualanId != $item->penjualan_id)
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $item->penjualan_kode }}</td>
                            <td>{{ $item->pembeli }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->penjualan_tanggal)->format('d/m/Y H:i') }}</td>
                            @php
                                $currentPenjualanId = $item->penjualan_id;
                            @endphp
                        @else
                            <td class="no-border"></td>
                            <td class="no-border"></td>
                            <td class="no-border"></td>
                            <td class="no-border"></td>
                        @endif
                        <td>{{ $detail->barang->barang_nama }}</td>
                        <td class="text-center">{{ $detail->jumlah }}</td>
                        <td class="text-right">Rp {{ number_format($detail->barang->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($detail->barang->harga_jual * $detail->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-right"><strong>Total Penjualan:</strong></td>
                <td class="text-right">
                    @php
                        $totalPenjualan = $penjualan->sum(function($item) {
                            return $item->details->sum(function($detail) {
                                return $detail->barang->harga_jual * $detail->jumlah;
                            });
                        });
                    @endphp
                    Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>