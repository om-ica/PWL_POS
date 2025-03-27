<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran</title>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck Bootstrap -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Tema AdminLTE -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition register-page">
    <div class="register-box"> 
        <div class="card card-outline card-primary"> 
          <div class="card-header text-center">
            <a href="{{ url('/') }}" class="h1"><b>Admin</b>LTE</a>
          </div> 
          <div class="card-body"> 
            <p class="register-box-msg">Create New Account</p>

            <form id="form-register" action="{{ url('register') }}" method="POST">
                @csrf

                <!-- Pilih Level -->
                <div class="form-group">
                    <div class="input-group mb-3">
                        <select name="level_id" class="form-control" required>
                            <option value="">- Select Level -</option>
                            @foreach($level as $item)
                                <option value="{{ $item->level_id }}">{{ $item->level_nama }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-users"></span>
                            </div>
                        </div>
                    </div>
                    <span id="error-level_id" class="text-danger error-text"></span>
                </div>
                
                <!-- Username -->
                <div class="form-group">
                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username" value="{{ old('username') }}" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <span id="error-username" class="text-danger error-text"></span>
                </div>

                <!-- Nama -->
                <div class="form-group">
                    <div class="input-group mb-3">
                        <input type="text" name="nama" class="form-control" placeholder="Name" value="{{ old('nama') }}" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-id-card"></span>
                            </div>
                        </div>
                    </div>
                    <span id="error-nama" class="text-danger error-text"></span>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <span id="error-password" class="text-danger error-text"></span>
                </div>

                <!-- Tombol Aksi -->
                <div class="row d-flex justify-content-between">
                    <div class="col-6">
                        <a href="{{ url('login') }}" class="btn btn-warning btn-block">Back</a>
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

<script>
    $(document).ready(function() { 
        $("#form-register").validate({ 
            rules: { 
                level_id: { required: true, number: true }, 
                username: { required: true, minlength: 3, maxlength: 20 }, 
                nama: { required: true, minlength: 3, maxlength: 100 }, 
                password: { required: true, minlength: 5, maxlength: 20 } 
            }, 
            submitHandler: function(form) { 
                $.ajax({ 
                    url: form.action, 
                    type: form.method, 
                    data: $(form).serialize(), 
                    success: function(response) { 
                        if (response.status) { 
                            Swal.fire({ 
                                icon: 'success', 
                                title: 'Pendaftaran Berhasil', 
                                text: response.message 
                            }).then(() => {
                                window.location.href = "{{ url('login') }}";
                            });
                        } else { 
                            $('.error-text').text(''); 
                            $.each(response.msgField, function(prefix, val) { 
                                $('#error-' + prefix).text(val[0]); 
                            }); 
                            Swal.fire({ 
                                icon: 'error', 
                                title: 'Terjadi Kesalahan', 
                                text: response.message 
                            }); 
                        } 
                    }              
                }); 
                return false; 
            }, 
            errorElement: 'span', 
            errorPlacement: function (error, element) { 
                error.addClass('invalid-feedback'); 
                element.closest('.form-group').append(error); 
            }, 
            highlight: function (element) { 
                $(element).addClass('is-invalid'); 
            }, 
            unhighlight: function (element) { 
                $(element).removeClass('is-invalid'); 
            } 
        }); 
    }); 
</script>

</body>
</html>