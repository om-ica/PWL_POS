<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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

    <h3 class="text-center font-12 font-bold">LAPORAN DATA STOK BARANG</h3>
    <p class="text-center font-10">Tanggal Cetak: {{ date('d F Y') }}</p>

    <table class="border-all">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal Penambahan</th>
                <th width="20%">Nama Supplier</th>
                <th width="25%">Nama Barang</th>
                <th width="15%">Jumlah Stok</th>
                <th width="20%">User</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stok as $s)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($s->stok_tanggal)->translatedFormat('d F Y') }}</td>
                <td>{{ $s->supplier ? $s->supplier->supplier_nama : 'Supplier Tidak Ditemukan' }}</td>
                <td>{{ $s->barang ? $s->barang->barang_nama : 'Barang Tidak Ditemukan' }}</td>
                <td class="text-center">{{ number_format($s->stok_jumlah, 0, ',', '.') }}</td>
                <td>{{ $s->user ? $s->user->username : 'User Tidak Ditemukan' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data stok barang.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>