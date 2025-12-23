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
    <h1 class="h3 mb-2 text-gray-800">Create General Journal Transactions</h1>

        @php
            $startOfMonth = now()->startOfMonth()->format('Y-m-d');

            $today = now();
            $GEJCode = 'GEJ' . $today->format('dmY') . 'xxx';
        @endphp

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('generaljournal.store') }}">
                @csrf

                <div class="form-group">
                    <label>General Journal Code</label>
                    <input type="text" name="gejcode" class="form-control" value="{{ $GEJCode }}" readonly>
                </div>

                <div class="form-group">
                    <label>Tanggal Jurnal</label>
                    <input type="date" name="journal_date" class="form-control" value="{{ $startOfMonth }}" required>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <input type="text" name="description" class="form-control" required>
                </div>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="journalTable">
                        <thead>
                            <tr>
                                <th>Akun</th>
                                <th>Posisi</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="account_id[]" class="form-control" required>
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">
                                                {{ $acc->account_code }} - {{ $acc->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="position[]" class="form-control" required>
                                        <option value="debet">Debet</option>
                                        <option value="kredit">Kredit</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="amount[]" class="form-control" required>
                                </td>
                                <td>
                                    <button class="btn btn-danger js-remove-row">Hapus</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" id="addRow" class="btn btn-secondary">Tambah Akun</button>
                        <br>
                        <br>
                <button type="submit" class="btn btn-primary">Simpan Jurnal</button>
            </form>
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
    $('#addRow').on('click', function(e){
        let table = document.querySelector('#journalTable tbody');
        let row = table.rows[0].cloneNode(true);
        row.querySelectorAll('input').forEach(input => input.value = '');
        table.appendChild(row);
    })

    $(document).on('click', '.js-remove-row', function () {
        if ($('#journalTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
        }
    });


</script>
@endsection