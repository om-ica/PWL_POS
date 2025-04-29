<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Detail Penjualan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Kode Penjualan</th>
                    <td>{{ $penjualan->penjualan_kode }}</td>
                </tr>
                <tr>
                    <th>Nama Pembeli</th>
                    <td>{{ $penjualan->pembeli }}</td>
                </tr>
                <tr>
                    <th>Tanggal Penjualan</th>
                    <td>{{ \Carbon\Carbon::parse($penjualan->penjualan_tanggal)->translatedFormat('d F Y H:i') }}</td>
                </tr>
                <tr>
                    <th>User</th>
                    <td>{{ $penjualan->user ? $penjualan->user->nama : 'Pengguna Tidak Ditemukan' }}</td>
                </tr>
            </table>

            <h5>Daftar Barang</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Harga Jual</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualan->details as $detail)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $detail->barang ? $detail->barang->barang_nama : 'Barang Tidak Ditemukan' }}</td>
                            <td class="text-right">Rp {{ number_format($detail->barang->harga_jual, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $detail->jumlah }}</td>
                            <td class="text-right">Rp {{ number_format($detail->barang->harga_jual * $detail->jumlah, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada barang yang dibeli.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-right">Total:</th>
                        <th class="text-right">Rp {{ number_format($penjualan->details->sum(fn($detail) => $detail->barang->harga_jual * $detail->jumlah), 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Tutup</button>
        </div>
    </div>
</div>