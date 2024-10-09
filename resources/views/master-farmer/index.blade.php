@extends('adminlte::page')

@section('title', 'Master Petani')

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
    <h1>List Master Petani</h1>
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
                <a href="{{ url('/master-data/master-farmer/add') }}" class="btn btn-success btn-sm float-right">+
                    Tambah</a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="list_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Nama Petani</th>
                                <th>NO. Telp</th>
                                <th>Luas Lahan</th>
                                <th>Tipe Tanah</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>


        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">PETA LOKASI</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="map" style="width: 100%; height:400px;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
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



            let map;
            let polygonLayer = null;


            function getPolygonCenter(coords) {
                let latSum = 0;
                let lngSum = 0;
                let numCoords = coords.length;

                for (let i = 0; i < numCoords; i++) {
                    latSum += coords[i][0]; // Latitude
                    lngSum += coords[i][1]; // Longitude
                }

                return [latSum / numCoords, lngSum / numCoords];
            }

            $('body').on('click', '.btn-map', function() {
                let polygonCoords = [];
                let coordString = '';

                let location = $(this).data('location');
                coordString = location;
                let coordArray = coordString.split(',');

                for (let i = 0; i < coordArray.length; i += 2) {
                    polygonCoords.push([parseFloat(coordArray[i]), parseFloat(coordArray[i + 1])]);
                }

                let centerCoord = getPolygonCenter(polygonCoords);

                $('#myModal').modal('show');
                setTimeout(function() {
                    map.invalidateSize();
                }, 500);

                if (!map) {
                    map = L.map('map').setView(centerCoord, 18);

                    let tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);
                } else {
                    map.setView(centerCoord, 18);
                }

                if (polygonLayer) {
                    map.removeLayer(polygonLayer);
                }

                polygonLayer = L.polygon(polygonCoords, {
                    color: 'red'
                }).addTo(map);
            });





            $('#list_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/master-data/master-farmer/list-data') }}",
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'handphone_number',
                        name: 'handphone_number'
                    },
                    {
                        data: 'land_area',
                        name: 'land_area'
                    },
                    {
                        data: 'land_type',
                        name: 'land_type'
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
