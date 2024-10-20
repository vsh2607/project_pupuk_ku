@extends('adminlte::page')

@section('title', 'Rencana Tanam')

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
    <h1>List Rencana Tanam</h1>
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
                <a href="{{ url('/module-management/planting-plan/add') }}" class="btn btn-success btn-sm float-right">+
                    Tambah</a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="list_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Nama Petani</th>
                                <th>Tanggal Rencana Tanam</th>
                                <th>Luas Rencana Tanam</th>
                                <th>Tanaman</th>
                                <th>Kebutuhan Rencana Pupuk</th>
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
                    url: "{{ url('/module-management/planting-plan/list-data') }}",
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
                        data: 'farmer_name',
                        name: 'farmer_name'
                    },
                    {
                        data: 'planned_date',
                        name: 'planned_date'
                    },
                    {
                        data : 'land_area',
                        name : 'land_area'
                    },
                    {
                        data: 'planned_plant',
                        name: 'planned_plant'
                    },
                    {
                        data: 'fertilizer_needs',
                        name: 'fertilizer_needs'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ]

            });
        })
    </script>
@stop
