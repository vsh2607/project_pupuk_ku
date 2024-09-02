@extends('adminlte::page')

@section('title', 'Module Distribusi Pupuk')

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
    <h1>Module Distribusi Pupuk</h1>
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
                    class="btn btn-success btn-md float-right" >+ Tambah</a>
                <button class="btn btn-info btn-md btn-show-map float-right" style="margin-right: 10px" type="button"><i class="fas fa-map"></i>
                    Tampilkan Peta</button>
            </div>

            <div class="card-body">
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="table" id="list_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Nama Petani</th>
                                <th>Luas Lahan</th>
                                <th>Jenis Lahan</th>
                                <th>Jenis Tanaman</th>
                                <th>Jumlah Pupuk (Dipunya)</th>
                                <th>Jumlah Pupuk (Meminjam)</th>
                                <th>Jumlah Pupuk (Dipinjam)</th>
                                <th>Jumlah Pupuk (Dibutuhkan)</th>
                                <th>Sisa Pupuk</th>
                                <th>Status</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">PETA DISTRIBUSI</h4>
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



    </div>



@stop

@section('plugins.Datatables', true)
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/leafletjs/leaflet.js"></script>

    <script>
        $(document).ready(function() {

            let mapData = @json($mapData);


            let map;
            let polygonLayer = [];

            function initMap(centerCoord) {
                map = L.map('map').setView(centerCoord, 15);

                let tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);
            }

            $('.btn-show-map').click(function() {
                $('#myModal').modal('show');

                if (!map) {
                    let firstCoordArray = mapData[0].land_location.split(',');
                    let centerCoord = [parseFloat(firstCoordArray[0]), parseFloat(firstCoordArray[1])];
                    initMap(centerCoord);

                    setTimeout(function() {
                        map.invalidateSize();
                    }, 500);
                }

                polygonLayer.forEach(layer => {
                    map.removeLayer(layer);
                });
                polygonLayer = [];

                mapData.forEach(e => {

                })

                mapData.forEach(element => {
                    let coordArray = element.land_location.split(',');
                    let polygonCoords = [];

                    for (let i = 0; i < coordArray.length; i += 2) {
                        polygonCoords.push([parseFloat(coordArray[i]), parseFloat(coordArray[i +
                            1])]);
                    }


                    let polygon = L.polygon(polygonCoords, {
                        color: 'red'
                    }).addTo(map);

                    let information = "Informasi Lahan <br>" +
                        `Pemilik : ${element.name} <br>` +
                        `Luas Lahan : ${element.land_area} m<sup>2</sup> <br>` +
                        `Jumlah Kebutuhan Pupuk : ${element.fertilizer_quantity_needed} KG </br>` +
                        `Jumlah Pupuk Yang Dimiliki :   ${element.fertilizer_quantity_owned} KG`

                    polygon.bindPopup(information);

                    polygonLayer.push(polygon);
                });
            });



            $('#list_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/module-management/fertilizer-distribution/list-data') }}",
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
                        data: 'land_area',
                        name: 'land_area'
                    },
                    {
                        data: 'land_type',
                        name: 'land_type'
                    },
                    {
                        data: 'plant_type',
                        name: 'plant_type'
                    },
                    {
                        data: 'fertilizer_quantity_owned',
                        name: 'fertilizer_quantity_owned'
                    },
                    {
                        data: 'fertilizer_quantity_needed',
                        name: 'fertilizer_quantity_needed'
                    },
                    {
                        data: 'fertilizer_quantity_remainder',
                        name: 'fertilizer_quantity_remainder'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    }

                ]

            });
        })
    </script>
@stop
