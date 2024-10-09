@extends('adminlte::page')

@section('title', 'Info Petani')

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
    <h1>Info Petani</h1>
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
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="form-group">
                            <label class="required" for="name">Nama Petani</label>
                            <input type="text" required name="name" id="name" class="form-control my-input"
                                placeholder="Nama Petani" value="{{ $data->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="form-group">
                            <label class="required" for="land_type">Tipe Lahan</label>
                            <input type="text" name="land_type" id="land_type" class="form-control"
                                value="{{ $data->land_type == 'OWNED' ? 'MILIK SENDIRI' : 'GARAP ORANG LAIN' }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="form-group">
                            <label class="required" for="handphone_number">Nomor Handphone</label>
                            <input type="text" required name="handphone_number" id="handphone_number"
                                class="form-control my-input-int" placeholder="Nomor Handphone"
                                value="{{ $data->handphone_number }}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="form-group">
                            <label class="required" for="land_area">Luas Lahan (m<sup>2</sup>)</label>
                            <input type="text" required name="land_area" id="land_area" class="form-control my-input-int"
                                placeholder="Luas Lahan" value="{{ $data->land_area }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="form-group">
                            <label for="land_location" class="required">Lokasi Lahan</label>
                            <div class="input-group">
                                <input required type="text" name="land_location" id="land_location"
                                    class="form-control my-input" placeholder="Lokasi Lahan"
                                    value="{{ $data->land_location }}" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-secondary btn-map" type="button">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12">
                        <div class="form-group">
                            <label class="required" for="plant_type">Jenis Tanaman</label>
                            <select name="plant_type[]" multiple="multiple" id="plant_type" class="form-control" required readonly>
                                @foreach ($plants as $plant)
                                    <option value="{{ $plant->plant->id }}"
                                        {{ in_array($plant->plant->id, $selectedPlants) ? 'selected' : '' }}>
                                        {{ $plant->plant->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


                <hr>
                    <p style="font-weight: bold;">Daftar Pupuk Kepemilikan</p>
                    <hr>
                    <div>
                        <table class="table" style="width: 100%;" id="table-fertilizer">
                            <thead>
                                <tr>

                                    <th>JENIS PUPUK</th>
                                    <th>QTY DIMILIKI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fertilizers as $key => $item)
                                    <tr>
                                        <td>
                                            <select name="fertilizer_name[]" class="form-control fertilizer-select" style="width: 100%;" readonly disabled>
                                                <option value="{{ $item->id_master_fertilizer }}">
                                                    {{ $item->MasterFertilizer->name }}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="fertilizer_qty_owned[]"
                                                class="form-control my-input-decimal" value="{{ $item->quantity_owned }}" readonly disabled>
                                        </td>
                                @endforeach
                            </tbody>
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

            $('#plant_type').select2({
                disabled: true
            });

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


            let polygonLayer = null;
            let polygonCoords = [];
            let coordString = '';

            let location = $("#land_location").val();
            coordString = location;
            let coordArray = coordString.split(',');

            for (let i = 0; i < coordArray.length; i += 2) {
                polygonCoords.push([parseFloat(coordArray[i]), parseFloat(coordArray[i + 1])]);
            }

            let centerCoord = getPolygonCenter(polygonCoords);

            let map = L.map('map').setView(centerCoord, 17);


            let tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            polygonLayer = L.polygon(polygonCoords, {
                color: 'red'
            }).addTo(map);


            $('.btn-map').click(function() {
                $('#myModal').modal('show');
                setTimeout(function() {
                    map.invalidateSize()
                }, 500);
            });

            $('.btn-save-map').click(function() {
                $('#land_location').val('');
                $('#land_location').val(polygonCoords);
                $('#myModal').modal('hide');
            });

            $('.btn-reset-map').click(function() {
                polygonCoords = [];
                if (polygonLayer) {
                    map.removeLayer(polygonLayer);
                }

                if (window.polygon) {
                    map.removeLayer(window.polygon);
                }
            });


            $(".my-input").on('input', function() {
                $(this).val($(this).val().toUpperCase());
            })

            $(".my-input-int").on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            $(".my-input-decimal").on('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '');
            });
        });
    </script>
@stop
