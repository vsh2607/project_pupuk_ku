@extends('adminlte::page')

@section('title', 'Tambah Data Petani')

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
    <h1>Tambah Data Petani</h1>
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
        <form method="POST">
            <div class="card">
                <div class="card-body">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="required" for="name">Nama Petani</label>
                                <input type="text" required name="name" id="name" class="form-control my-input"
                                    placeholder="Nama Petani">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="required" for="land_type">Tipe Lahan</label>
                                <select name="land_type" id="land_type" class="form-control">
                                    <option value="" disabled selected>--PILIH--</option>
                                    <option value="OWNED">MILIK SENDIRI</option>
                                    <option value="LEASED">GARAP ORANG LAIN</option>
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="required" for="handphone_number">Nomor Handphone</label>
                                <input type="text" required name="handphone_number" id="handphone_number"
                                    class="form-control my-input-int" placeholder="Nomor Handphone">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="required" for="land_area">Luas Lahan (m<sup>2</sup>)</label>
                                <input type="text" required name="land_area" id="land_area" class="form-control my-input-int"
                                    placeholder="Luas Lahan">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label for="land_location" class="required">Lokasi Lahan</label>
                                <div class="input-group">
                                    <input required type="text" name="land_location" id="land_location"
                                        class="form-control my-input" placeholder="Lokasi Lahan">
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
                                <label class="required" for="fertilizer_quantity_owned">Jumlah Pupuk Dimiliki (KG)</label>
                                <input required type="text" name="fertilizer_quantity_owned" id="fertilizer_quantity_owned"
                                    class="form-control my-input-decimal" placeholder="Jumlah Pupuk Dimiliki">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="required" for="fertilizer_quantity_needed">Jumlah Pupuk Dibutuhkan (KG)</label>
                                <input required type="text" name="fertilizer_quantity_needed" id="fertilizer_quantity_needed"
                                    class="form-control my-input-decimal" placeholder="Jumlah Pupuk Dibutuhkan">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="required" for="plant_type">Jenis Tanaman</label>
                                <select name="plant_type[]" multiple="multiple" id="plant_type"  class="form-control" required>
                                </select>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer">
                    <button class="btn btn-primary" type="submit">SUBMIT</button>
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
                            <button type="button" class="btn btn-danger btn-reset-map">Reset</button>
                            <button type="button" class="btn btn-success btn-save-map">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>


        </form>
    </div>


@stop

@section('plugins.Datatables', true)
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/leafletjs/leaflet.js"></script>
    <script>
        $(document).ready(function() {

            $("#land_type").select2();


            $('#plant_type').select2({
                ajax: {
                    url: "{{ url('resources/list-all-plant') }}",
                    data: function(params) {
                        var query = {
                            name: params.term
                        };
                        return query;
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        var processedData = $.map(data, function(obj) {
                            obj.id = obj.id;
                            obj.text = obj.name;
                            return obj;
                        });
                        return {
                            results: processedData,
                        };
                    },
                },
                minimumInputLength: 0,

            });


            let map = L.map('map').setView([-7.775079291369336, 110.44946003549626], 20);
            let polygonCoords = [];

            let tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);


            map.on('click', function(e) {
                polygonCoords.push([e.latlng.lat, e.latlng.lng]);

                if (polygonCoords.length >= 3) {
                    if (window.polygon) {
                        map.removeLayer(window.polygon);
                    }

                    window.polygon = L.polygon(polygonCoords, {
                        color: 'red'
                    }).addTo(map);
                }
            });




            $('.btn-map').click(function() {
                $('#myModal').modal('show');
                setTimeout(function() {
                    map.invalidateSize()
                }, 500);
            });

            $('.btn-save-map').click(function(){
                $('#land_location').val(polygonCoords);
                $('#myModal').modal('hide');
            });

            $('.btn-reset-map').click(function() {
                polygonCoords = [];
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
