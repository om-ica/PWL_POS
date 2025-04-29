@empty($penjualan)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan.
                </div>
                <a href="{{ url('/penjualan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-edit-penjualan">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Nama Pembeli</label>
                        <input type="text" name="pembeli" id="pembeli" class="form-control" required
                            value="{{ $penjualan->pembeli }}">
                        <small id="error-pembeli" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Penjualan</label>
                        <input type="datetime-local" name="penjualan_tanggal" id="penjualan_tanggal" class="form-control" required
                            value="{{ \Carbon\Carbon::parse($penjualan->penjualan_tanggal)->format('Y-m-d\TH:i') }}">
                        <small id="error-penjualan_tanggal" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Detail Barang</label>
                        <table class="table table-bordered" id="table-items">
                            <thead>
                                <tr>
                                    <th width="30%">Nama Barang</th>
                                    <th width="15%">Real Stok</th>
                                    <th width="15%">Harga Jual</th>
                                    <th width="15%">Jumlah</th>
                                    <th width="15%">Subtotal</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                    <td id="total-harga">Rp 0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">Tambah Barang</button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let itemCounter = 0;
        const barangData = @json($barangData);

        function addItem(barangId = '', jumlah = '') {
            itemCounter++;
            const selectedBarang = barangId ? barangData.find(b => b.id == barangId) : null;
            const hargaJual = selectedBarang ? selectedBarang.harga_jual : 0;
            const realStok = selectedBarang ? selectedBarang.real_stok : 0;

            // Bangun opsi untuk <select> menggunakan JavaScript
            let options = '<option value="">- Pilih Barang -</option>';
            barangData.forEach(item => {
                const isSelected = barangId == item.id ? 'selected' : '';
                options += `<option value="${item.id}" ${isSelected}>${item.nama}</option>`;
            });

            const row = `
                <tr id="item-row-${itemCounter}">
                    <td>
                        <select name="items[${itemCounter}][barang_id]" class="form-control barang-select" required onchange="updateHarga(${itemCounter})">
                            ${options}
                        </select>
                        <small class="error-text form-text text-danger" id="error-items-${itemCounter}-barang_id"></small>
                    </td>
                    <td class="real-stok text-center">${realStok}</td>
                    <td class="harga-jual text-right">Rp ${new Intl.NumberFormat('id-ID').format(hargaJual)}</td>
                    <td>
                        <input type="number" name="items[${itemCounter}][jumlah]" class="form-control jumlah" value="${jumlah || ''}" min="1" required>
                        <small class="error-text form-text text-danger" id="error-items-${itemCounter}-jumlah"></small>
                    </td>
                    <td class="subtotal text-right">Rp 0</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${itemCounter})">Hapus</button>
                    </td>
                </tr>
            `;

            $('#table-items tbody').append(row);
            updateTotal();
        }

        function updateHarga(counter) {
            const barangId = $(`#item-row-${counter} .barang-select`).val();
            const selectedBarang = barangData.find(b => b.id == barangId);
            const hargaJual = selectedBarang ? selectedBarang.harga_jual : 0;
            const realStok = selectedBarang ? selectedBarang.real_stok : 0;

            $(`#item-row-${counter} .harga-jual`).text('Rp ' + new Intl.NumberFormat('id-ID').format(hargaJual));
            $(`#item-row-${counter} .real-stok`).text(realStok);
            updateTotal();
        }

        function removeItem(counter) {
            $(`#item-row-${counter}`).remove();
            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            $('#table-items tbody tr').each(function() {
                const barangId = $(this).find('.barang-select').val();
                const selectedBarang = barangData.find(b => b.id == barangId);
                const hargaJual = selectedBarang ? selectedBarang.harga_jual : 0;
                const jumlah = parseInt($(this).find('.jumlah').val()) || 0;
                const subtotal = hargaJual * jumlah;
                $(this).find('.subtotal').text('Rp ' + new Intl.NumberFormat('id-ID').format(subtotal));
                total += subtotal;
            });
            $('#total-harga').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
        }

        // Reset dan isi ulang data saat konten modal dimuat
        $(document).ready(function() {
            // Reset form dan tabel
            const form = $('#form-edit-penjualan');
            if (form.length) {
                form[0].reset();
                $('#pembeli').val('{{ $penjualan->pembeli }}');
                $('#penjualan_tanggal').val('{{ \Carbon\Carbon::parse($penjualan->penjualan_tanggal)->format('Y-m-d\TH:i') }}');
            }
            $('#table-items tbody').empty();
            $('#total-harga').text('Rp 0');
            itemCounter = 0;

            // Isi ulang data dari penjualan yang dipilih
            @foreach($penjualan->details as $detail)
                addItem('{{ $detail->barang_id }}', '{{ $detail->jumlah }}');
            @endforeach

            $(document).on('input', '.jumlah', function() {
                updateTotal();
            });

            $("#form-edit-penjualan").on('submit', function(e) {
                e.preventDefault();
                $('.error-text').text('');

                $.ajax({
                    url: "{{ url('/penjualan/' . $penjualan->penjualan_id . '/update_ajax') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            dataPenjualan.ajax.reload();
                        } else {
                            $.each(response.msgField, function(prefix, val) {
                                if (prefix.includes('items')) {
                                    const parts = prefix.split('.');
                                    const index = parts[1];
                                    const field = parts[3];
                                    $(`#error-items-${index}-${field}`).text(val[0]);
                                } else {
                                    $(`#error-${prefix}`).text(val[0]);
                                }
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal menyimpan perubahan penjualan.'
                        });
                    }
                });
            });
        });
    </script>
@endempty