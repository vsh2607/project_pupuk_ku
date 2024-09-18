@extends('adminlte::page')

@section('title', 'List Periode Distribusi Pupuk')

@section('adminlte_css_pre')
    <link rel="icon" href="{{ asset('assets/logo.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('leafletjs/leaflet.css') }}">
    <style>
        .required::after {
            content: '*';
            color: red;
        }
    </style>
@endsection

@section('content_header')
    <h1>List Periode Distribusi Pupuk</h1>
@stop
@section('content')
    @if (session('error'))
        <div class="alert alert-danger mb-2">
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success mb-2">
            {{ session('success') }}
        </div>
    @endif
    <div class="container-fluid" style="margin-top:20px; text-transform: uppercase;">
        <div class="card">
            <div class="card-header">
                <a href="{{ url('/module-management/fertilizer-distribution/add') }}"
                    class="btn btn-success btn-md float-right">+ Tambah</a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="list_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Periode</th>
                                <th>Tanggal Periode</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>



@stop

@section('plugins.Datatables', true)
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/leafletjs/leaflet.js"></script>
    <script>
        $(document).ready(function() {

            $('#list_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/module-management/fertilizer-distribution-periode/list-data-periode') }}",
                },
                columnDefs: [{
                    "targets": [0],
                    "visible": true,
                    "searchable": false,
                    "orderable": false,
                }, ],
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'periode',
                        name: 'periode'
                    },
                    {
                        data: 'periode_date',
                        name: 'periode_date'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },

                ]

            });
        })
    </script>
@stop
