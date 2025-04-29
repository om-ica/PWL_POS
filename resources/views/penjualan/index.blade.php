@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Penjualan</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('penjualan/create_ajax') }}')" class="btn btn-success">Tambah Penjualan (Ajax)</button>
                <a href="{{ url('/penjualan/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Penjualan</a>
                <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Penjualan</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered table-sm table-striped table-hover" id="table-penjualan">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Penjualan</th>
                        <th>Pembeli</th>
                        <th>Tanggal</th>
                        <th>Total Harga</th>
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
    .penjualan-buttons {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    .penjualan-buttons button {
        padding: 2px 8px;
        font-size: 12px;
    }
    .penjualan-number {
        min-width: 30px;
        text-align: center;
    }
</style>
@endpush

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').html('');
        $('#myModal').html('<div class="modal-dialog"><div class="modal-content"><div class="modal-body">Loading...</div></div></div>');

        $('#myModal').load(url, function(response, status, xhr) {
            if (status == "error") {
                console.log("Error loading modal: " + xhr.status + " " + xhr.statusText);
                console.log(xhr.responseText);
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

    var dataPenjualan;
    $(document).ready(function(){
        dataPenjualan = $('#table-penjualan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "{{ url('penjualan/list') }}",
                "dataType": "json",
                "type": "POST",
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
                    data: "penjualan_kode",
                    className: "",
                    width: "15%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "pembeli",
                    className: "",
                    width: "15%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "penjualan_tanggal",
                    className: "",
                    width: "15%",
                    orderable: true,
                    searchable: false,
                    render: function(data, type, row) {
                        const date = new Date(data);
                        return new Intl.DateTimeFormat('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                            hour: 'numeric',
                            minute: 'numeric'
                        }).format(date);
                    }
                },
                {
                    data: "total_harga",
                    class防范Name: "text-right",
                    width: "15%",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "user.nama",
                    className: "text-center",
                    width: "15%",
                    orderable: true,
                    searchable: true,
                    render: function(data, type, row) {
                        return row.user ? row.user.nama : 'Pengguna Tidak Ditemukan';
                    }
                },
                {
                    data: "aksi",
                    className: "text-center",
                    width: "20%",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#myModal').on('hidden.bs.modal', function () {
            $('#myModal').html('');
        });
    });
</script>
@endpush