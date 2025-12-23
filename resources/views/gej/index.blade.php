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
    <h1 class="h3 mb-2 text-gray-800">List of General Journal Transactions</h1>

    @php
        $startOfMonth = now()->startOfMonth()->format('Y-m-d');
        $endOfMonth   = now()->endOfMonth()->format('Y-m-d');
    @endphp


    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <input type="date" id="from_date" class="form-control" value="{{ $startOfMonth }}">
                </div>
                <div class="col-md-3">
                    <input type="date" id="to_date" class="form-control" value="{{ $endOfMonth }}">
                </div>
                <div class="col-md-3">
                    <button id="filter" class="btn btn-primary">Search</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button type="button" class="btn btn-primary" id="btn-add"><i class="typcn typcn-plus"></i> General Journal Transactions </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="journalTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kode Jurnal</th>
                            <th>Keterangan</th>
                            <th>Total Debet</th>
                            <th>Total Kredit</th>
                        </tr>
                    </thead>
                </table>
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
    $(document).ready(function () {

        let table = $('#journalTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{!! url()->current() !!}",
                data: function (d) {
                    d.from_date = $('#from_date').val();
                    d.to_date  = $('#to_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'journal_date' },
                { data: 'journal_code' },
                { data: 'description' },
                { data: 'total_debet', className: 'text-end' },
                { data: 'total_kredit', className: 'text-end' },
            ]
        });

        $('#filter').click(function () {
            table.draw();
        });

        $('body').on('click', '#btn-add', function() {
            console.log("Tombol Tambah diklik"); // Debugging
            window.location.href = "{{ route('generaljournal.create') }}";
        });
    });
</script>
@endsection