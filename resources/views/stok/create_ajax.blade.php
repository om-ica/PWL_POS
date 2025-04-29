<div class="modal-dialog modal-lg" role="document"> 
    <div class="modal-content"> 
        <div class="modal-header"> 
            <h5 class="modal-title" id="exampleModalLabel">Tambah Data Stok Barang</h5> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button> 
        </div> 
        <div class="modal-body"> 
            <form action="{{ url('/stok/ajax') }}" method="POST" id="form-tambah"> 
                @csrf
                <div class="form-group"> 
                    <label>Tanggal Penambahan</label> 
                    <input type="date" name="stok_tanggal" id="stok_tanggal" class="form-control" value="{{ now()->format('Y-m-d') }}" required> 
                    <small id="error-stok_tanggal" class="error-text form-text text-danger"></small> 
                </div> 
                <div class="form-group"> 
                    <label>Supplier Barang</label> 
                    <select name="supplier_id" id="supplier_id" class="form-control" required> 
                        <option value="">- Pilih Supplier -</option> 
                        @foreach($supplier as $s) 
                            <option value="{{ $s->supplier_id }}">{{ $s->supplier_nama }}</option> 
                        @endforeach 
                    </select> 
                    <small id="error-supplier_id" class="error-text form-text text-danger"></small> 
                </div> 
                <div class="form-group"> 
                    <label>Nama Barang</label> 
                    <select name="barang_id" id="barang_id" class="form-control" required> 
                        <option value="">- Pilih Barang -</option> 
                        @foreach($barang as $b) 
                            <option value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option> 
                        @endforeach 
                    </select> 
                    <small id="error-barang_id" class="error-text form-text text-danger"></small> 
                </div> 
                <div class="form-group"> 
                    <label>Jumlah Stok</label> 
                    <input type="number" name="stok_jumlah" id="stok_jumlah" class="form-control" min="0" required> 
                    <small id="error-stok_jumlah" class="error-text form-text text-danger"></small> 
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
    $(document).ready(function() { 
        $("#form-tambah").on('submit', function(e) {
            e.preventDefault(); // Mencegah form submit biasa

            // Reset pesan error
            $('.error-text').text('');

            $.ajax({ 
                url: $(this).attr('action'), 
                type: $(this).attr('method'), 
                data: $(this).serialize(), 
                success: function(response) { 
                    if (response.status) { 
                        $('#myModal').modal('hide'); 
                        Swal.fire({ 
                            icon: 'success', 
                            title: 'Berhasil', 
                            text: response.message 
                        }); 
                        dataStok.ajax.reload(); // Refresh tabel stok
                    } else { 
                        // Tampilkan pesan error validasi
                        $.each(response.msgField, function(prefix, val) {
                            $('#error-' + prefix).text(val[0]);
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
                        text: 'Gagal menyimpan data stok.'
                    }); 
                } 
            }); 
        }); 
    }); 
</script>