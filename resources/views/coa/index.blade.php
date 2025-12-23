@extends('layout.master')

@section('css')
<!-- Bootstrap CSS & JS (sesuai versi yang Anda pakai) -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- Bootstrap Timepicker CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
<link href="{{ asset('assets/master/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">List of Chart Of Account</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button type="button" class="btn btn-primary" id="btn-add"><i class="typcn typcn-plus"></i> New COA </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No. </th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">New COA</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addForm">
                    @csrf
                    <div class="form-group">
                        <label for="code">Code</label>
                        <input type="text" class="form-control" id="code" name="code" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" id="type" class="form-control">
                            <option value="">Pilih</option>
                            <option value="debet">Debet</option>
                            <option value="kredit">Kredit</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-save" style="display: none;">Save</button>
                <button type="button" class="btn btn-primary" id="btn-update" style="display: none;">Update</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script-bottom')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.1/moment.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<script src="{{ asset('assets/master/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('assets/master/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

<script>
    $(document).ready(function (){
        var datatable = $('#dataTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ordering: true,
            ajax: {
                url: '{!! url()->current() !!}',
            },
            columns:[
                { "data": 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'account_code', name: 'account_code' },
                { data: 'account_name', name: 'account_name' },
                { data: 'normal_balance', name: 'normal_balance' },
                { data: 'balance', name: 'balance', className: 'text-end' },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '15%' }
            ]
        })
    })
</script>
<script>
    $('body').on('click', '#btn-add', function() {
        $('#addModal').modal('show');

        let today = new Date();

        let day   = String(today.getDate()).padStart(2, '0');
        let month = String(today.getMonth() + 1).padStart(2, '0'); // bulan mulai dari 0
        let year  = today.getFullYear();

        let formattedDate = day + month + year;

        $('#code').val('COA' + formattedDate + 'xxx');
        $('#name').val("")
        $('#type').val("")

        $('#btn-save').show()
        $('#btn-update').hide()
    });

    $('#btn-save').on('click', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('listcoa.store') }}",
            type: "POST",

            data: {
                _token: "{{ csrf_token() }}",
                name: $('#name').val(),
                type: $('#type').val()
            },
                success: function(response){
                    if (response.success) {
                        Swal.fire("Berhasil!", response.message, "success");
                        $('#addModal').modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                    } else {
                        Swal.fire("Gagal!", response.message || "Terjadi kesalahan!", "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error!", "Terjadi kesalahan pada server!", "error");
                    console.log(xhr.responseText); // Debugging
                }
        })
    })

    $('body').on('click', '#edit', function(e) {
        e.preventDefault();
        
        let id = $(this).data('id');

        $.ajax({
            url: "{{ route('listcoa.edit', ':id') }}".replace(':id', id),
            type: "GET",
            success: function(response){
                $('#name').val(response.account_name);
                $('#code').val(response.account_code);
                $('#type').val(response.normal_balance);
                $('#btn-save').hide();
                $('#btn-update').show();

                // Tambahkan input hidden untuk idcoa jika belum ada
                if ($('#idcoa').length === 0) {
                    $('#addForm').prepend('<input type="hidden" id="idcoa" name="idcoa" value="' + response.id + '">');
                } else {
                    $('#idcoa').val(response.id);
                }

                $('#addModal').modal('show');
            }
        })
    })

    $('#btn-update').on('click', function(e){
        e.preventDefault();

        var id = $('#idcoa').val();

        $.ajax({
            url: "{{ route('listcoa.update', ':id') }}".replace(':id', id),
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                _method: "PUT",
                name: $('#name').val(),
                type: $('#type').val()
            },
                success: function(response){
                    if (response.success) {
                        Swal.fire("Berhasil!", response.message, "success");
                        $('#addModal').modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                    } else {
                        Swal.fire("Gagal!", response.message || "Terjadi kesalahan!", "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error!", "Terjadi kesalahan pada server!", "error");
                    console.log(xhr.responseText); // Debugging
                }
        })
    })

     $('body').on('click', '#delete', function(e){
            e.preventDefault();
            
            let id = $(this).data('id');

            Swal.fire({
                title: 'Yakin?',
                text: 'Data akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('listcoa.destroy', ':id') }}".replace(':id', id),
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response){
                            if (response.success) {
                                Swal.fire("Berhasil!", response.message, "success");
                                $('#dataTable').DataTable().ajax.reload();
                            } else {
                                Swal.fire("Gagal!", response.message || "Terjadi kesalahan!", "error");
                            }
                        },
                        error: function(xhr) {
                            Swal.fire("Error!", "Terjadi kesalahan pada server!", "error");
                            console.log(xhr.responseText);
                        }
                    })
                }
            });
        });
</script>
@endsection