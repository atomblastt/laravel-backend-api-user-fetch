
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="Thomas R">
        <meta name="generator" content="Hugo 0.84.0">
        <title>Peasy AI</title>

        <!-- Bootstrap core CSS -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
        <!-- Bootstrap Data Table -->
        <link href="{{ asset('assets/css/dataTables/dataTables.bootstrap5.css') }}">
        <link href="{{ asset('assets/css/dataTables/buttons.dataTables.css') }}">

        <!-- Favicons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.min.css" integrity="sha256-h2Gkn+H33lnKlQTNntQyLXMWq7/9XI2rlPCsLsVcUBs=" crossorigin="anonymous">
        <link rel="icon" href="{{ asset('assets/logo/logo.png')}}">
        <meta name="theme-color" content="#7952b3">


        <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        .vl {
            border-left: 2px solid black;
            height: auto;
        }

        .btn-primary {
            color: #fff !important;
            background-color: #668647 !important;
            border-color: #668647 !important;
        }

        .btn-danger {
            color: #fff !important;
            background-color: #992934eb !important;
            border-color: #992934eb !important;
        }

        .modal-header{
            background-color: #7aa20d !important;
            color: white !important;
        }

        .btn-sm {
            color: black !important;
        }

        .form-group{
            padding: inherit !important;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
            font-size: 3.5rem;
            }
        }
        </style>
    </head>
<body>
    <div class="py-md-5">
        <div class="container">
            <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
                <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
                    <img src="{{ asset('assets/logo/logo.png')}}" width="auto" height="32" class="me-2" viewBox="0 0 118 94" role="img">
                    <span class="fs-4" style="color: #7aa20d;">Peasy AI Test</span>
                </a>
            </header>

            <main>
                @if(Session::has('status'))
                    @if(Session::get('status') === 'success')
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ Session::get('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @elseif(Session::get('status') === 'error')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ Session::get('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                @endif
                
                @yield('content')

                {{-- Modal Start Here --}}
                <div class="modal fade" id="userPersonalDeleteModal" tabindex="-1" aria-labelledby="userPersonalDeleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="userPersonalDeleteModalLabel">Delete Data</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Modal End Here --}}
            </main>

            <footer class="pt-5 my-5 text-muted border-top">
                <span style="color: #7aa20d;"> Created by Thomas R &middot; &copy; 2024 </span>
            </footer>
        </div>
    </div>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>
    {{-- Data Tables --}}
    <script src="{{ asset('assets/js/dataTables/dataTables.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables/dataTables.buttons.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables/buttons.dataTables.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js" integrity="sha256-O11zcGEd6w4SQFlm8i/Uk5VAB+EhNNmynVLznwS6TJ4=" crossorigin="anonymous"></script>
    {{-- Custom JS --}}
    <script>
        /* Data Table */
        new DataTable('table.userTable');

        /* Trigger modal delete data in table */
        $('.user-personal-delete').click(function() {
            var userId = $(this).data('id');
            var userName = $(this).data('name');
            console.log(userId)
            console.log(userName)
            $('#userPersonalDeleteModal .modal-body').html('<p>Are you sure you want to delete the user by name: <br> <span style="color: #992934eb; font-weight:bold;">' + userName + '</span></p>');
            $('#userPersonalDeleteModal').data('user-id', userId);
        });
        
        /* Trigger button submit in modal DELETE user */
        $('#confirmDeleteButton').click(function() {
            var userId = $('#userPersonalDeleteModal').data('user-id');
            $.ajax({
                url: '{{ route("delete-user", ":id") }}'.replace(':id', userId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#userPersonalDeleteModal').modal('hide');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        function confirmDelete(userId, userName) {
            // Tampilkan Sweet Alert untuk konfirmasi penghapusan dengan nama pengguna
            Swal.fire({
                title: "Are you sure?",
                text: "You are going to delete " + userName + ". You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika pengguna menekan tombol "Yes, delete it!", kirim permintaan AJAX untuk menghapus data
                    $.ajax({
                        url: '{{ route("delete-user", ":id") }}'.replace(':id', userId),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // Tampilkan Sweet Alert sukses
                            Swal.fire({
                                title: "Deleted!",
                                text: "Your file has been deleted.",
                                icon: "success"
                            }).then(() => {
                                // Muat ulang halaman setelah menghapus data
                                location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            // Tampilkan Sweet Alert jika terjadi kesalahan saat menghapus data
                            Swal.fire({
                                title: "Error!",
                                text: "Failed to delete data.",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
