<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - PDF</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 20px;
            line-height: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            padding: 6px 8px;
            font-size: 11pt;
        }
        th {
            text-align: center;
            background-color: #f2f2f2;
            border: 1px solid #000;
        }
        .d-block {
            display: block;
        }
        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .p-1 {
            padding: 5px 1px 5px 1px;
        }
        .font-10 {
            font-size: 10pt;
        }
        .font-11 {
            font-size: 11pt;
        }
        .font-12 {
            font-size: 12pt;
        }
        .font-13 {
            font-size: 13pt;
        }
        .font-bold {
            font-weight: bold;
        }
        .border-bottom-header {
            border-bottom: 1px solid;
        }
        .border-all, .border-all th, .border-all td {
            border: 1px solid;
        }
    </style>
</head>
<body>
    <table class="border-bottom-header">
        <tr>
            <td width="15%" class="text-center"><img src="{{ asset('storage/polinema-bw.png') }}" class="image"></td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold mb-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</span>
                <span class="text-center d-block font-13 font-bold mb-1">POLITEKNIK NEGERI MALANG</span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span>
                <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101105, 0341-404420, Fax. (0341) 404420</span>
                <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span>
            </td>
        </tr>
    </table>

    <h1>Laporan Penjualan</h1>
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