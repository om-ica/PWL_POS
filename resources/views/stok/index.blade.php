@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Stok Barang</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('stok/import') }}')" class="btn btn-info">Import Stok Barang</button>
                <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Stok Barang</a>
                <a href="{{ url('/stok/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Stok Barang</a>
                <button onclick="modalAction('{{ url('/stok/create_ajax') }}')" class="btn btn-success">Tambah Data (Ajax)</button>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered table-sm table-striped table-hover" id="table-stok">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Penambahan</th>
                        <th>Nama Supplier</th>
                        <th>Nama Barang</th>
                        <th>Jumlah Stok</th>
                        <th>User</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static"
         data-keyboard="false" data-width="75%"></div>
@endsection

@push('css')
<style>
    .stok-buttons {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    .stok-buttons button {
        padding: 2px 8px;
        font-size: 12px;
    }
    .stok-number {
        min-width: 30px;
        text-align: center;
    }
</style>
@endpush

@push('js')
<script>
    function modalAction(url = '') {
        // Bersihkan konten modal sebelum memuat konten baru
        $('#myModal').html('');
        // Tampilkan loading spinner (opsional, untuk UX lebih baik)
        $('#myModal').html('<div class="modal-dialog"><div class="modal-content"><div class="modal-body">Loading...</div></div></div>');

        $('#myModal').load(url, function(response, status, xhr) {
            if (status == "error") {
                console.log("Error loading modal: " + xhr.status + " " + xhr.statusText);
                console.log(xhr.responseText); // Log respons server
                $('#myModal').html('<div class="modal-dialog"><div class="modal-content"><div class="modal-body">Gagal memuat konten: ' + xhr.statusText + '</div></div></div>');
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal memuat form: ' + xhr.statusText
                });
            } else {
                $('#myModal').modal('show');
            }
        });
    }

    var dataStok;
    $(document).ready(function(){
        dataStok = $('#table-stok').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ url('stok/list') }}",
                "dataType": "json",
                "type": "POST",
                "data": function (d) {
                    // Filter ini sementara dihapus karena tidak ada dropdown di view
                    // d.filter_kategori = $('.filter_kategori').val();
                }
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    className: "text-center",
                    width: "5%",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "stok_tanggal",
                    className: "",
                    width: "15%",
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        const date = new Date(data);
                        return new Intl.DateTimeFormat('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        }).format(date);
                    }
                },
                {
                    data: "supplier.supplier_nama",
                    className: "",
                    width: "13%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "barang.barang_nama",
                    className: "",
                    width: "20%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "stok_jumlah",
                    className: "text-center",
                    width: "13%",
                    orderable: true,
                    searchable: false,
                },
                {
                    data: "user.nama",
                    className: "text-center",
                    width: "14%",
                    orderable: true,
                    searchable: true,
                    render: function(data, type, row) {
                        return row.user ? row.user.nama : 'Pengguna Tidak Ditemukan';
                    }
                },
                {
                    data: "aksi",
                    className: "text-center",
                    width: "14%",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Bersihkan modal saat ditutup
        $('#myModal').on('hidden.bs.modal', function () {
            $('#myModal').html('');
        });
    });
</script>
@endpush