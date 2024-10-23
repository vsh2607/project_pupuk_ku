@extends('adminlte::page')

@section('title', 'Edit Rencana Tanam')

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
    <h1>Edit Rencana Tanam</h1>
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
        <form method="POST" autocomplete="off">
            <div class="card">
                <div class="card-body">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="required" for="id_master_farmer">Nama Petani</label>
                                <select name="id_master_farmer" id="id_master_farmer" class="form-control">
                                    <option value="{{ $data->id_master_farmer }}" selected>{{ $data->MasterFarmer->name }}
                                    </option>
                                </select>

                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="required" for="date">Tanggal Rencana Tanam</label>
                                <input type="date" required name="date" id="date" class="form-control my-input"
                                    placeholder="Tanggal Rencana Tanam" value="{{ $data->planned_date }}">

                            </div>
                        </div>

                    </div>
                    <div class="row">

                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="required" for="land_area">Luas Lahan Rencana (m<sup>2</sup>)</label>
                                <input type="text" required name="land_area" id="land_area"
                                    value="{{ $data->land_area }}" class="form-control my-input-int"
                                    placeholder="Luas Lahan">
                                <small id="max-land-area-cont" style="display: none;"><i
                                        id="max-land-area-text"></i></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">


                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="required" for="plant_type">Jenis Tanaman</label>
                                <select name="plant_type[]" multiple="multiple" id="plant_type" class="form-control"
                                    required>
                                </select>

                            </div>
                        </div>

                    </div>
                    <div class="row">


                    </div>

                    <hr>
                    <p style="font-weight: bold;">Daftar Rencana Kebutuhan Pupuk</p>
                    <hr>
                    <div>
                        <table class="table" style="width: 100%;" id="table-fertilizer">
                            <thead>
                                <tr>

                                    <th>JENIS PUPUK</th>
                                    <th>Qty Pupuk Yang dimiliki</th>
                                    <th>Qty Perencanaan</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <br>
                        <button class="btn btn-primary" type="button" style="width: 100%;" id="btnAddRow">
                            Tambah Jenis Pupuk
                        </button>
                    </div>


                </div>

                <div class="card-footer">
                    <button class="btn btn-primary" type="submit">SUBMIT</button>
                </div>
            </div>



        </form>
    </div>


@stop

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/leafletjs/leaflet.js"></script>
    <script>
        let maxLandArea = 0;
        $("#id_master_farmer").on('change', function() {
            let farmerId = $(this).val();
            $.ajax({
                url: `{{ url('resources/${farmerId}/get-farmer-fertilizer-plant-data') }}`,
                type: 'GET',
                success: function(response) {
                    $('#plant_type').empty().trigger('change');
                    $('#land_area').val('{{ $data->land_area }}');
                    $("#table-fertilizer tbody").empty();

                    $("#max-land-area-cont").css('display', '');
                    $("#max-land-area-text").html('Sisa Maximum Luas Lahan Tersedia : ' + (Number(
                        response.land_area) - response.total_land_planned + Number(
                        {{ $data->land_area }})) + ' m<sup>2</sup>');
                    maxLandArea = Number(response.land_area) - response.total_land_planned + Number(
                        {{ $data->land_area }});

                    let plantData = [];

                    let plantSelectedData = @json($data_plant)

                    response.farmer_plants.forEach(function(plant) {
                        plantData.push({
                            id: plant.plant.id,
                            text: plant.plant.name,
                            selected: plantSelectedData.includes(plant.plant.id)

                        });
                    });

                    $('#plant_type').select2({
                        data: plantData,
                        width: '100%'
                    });

                    let plannedData = @json($data->TDFarmerPlanned);


                    plannedData.forEach(function(plannedData) {
                        $("#table-fertilizer tbody").append(`
                        <tr>
                            <td>
                                <select name="fertilizer_name[]" class="form-control fertilizer-select" style="width=100%;" required>
                                    <option value="${plannedData.id_master_fertilizer}">${plannedData.master_fertilizer.name}</option>
                                </select>
                            </td>
                            <td><input type="text" name="fertilizer_qty_owned[]" id="fertilizer_qty_owned" class="form-control my-input-decimal" value="${plannedData.quantity_owned}" readonly></td>
                            <td><input type="text" name="fertilizer_qty_planned[]" id="fertilizer_qty_planned" class="form-control my-input-decimal" placeholder="Masukkan Qty Rencana" required value="${plannedData.quantity_planned}"></td>
                            <td><button class="btn btn-danger" id="btnDeleteRow"><i class="fas fa-trash"></i></button></td>
                        </tr>
                        `);
                    });


                    response.master_farmer_fertilizer.forEach(function(fertilizer) {

                    });

                },
                error: function(err) {
                    console.log(err);
                }

            })
        });
        $(document).ready(function() {



            $("#id_master_farmer").val('{{ $data->id_master_farmer }}').trigger('change');

            $("#plant_type").select2({
                width: '100%'
            });



            function checkDuplicateFertilizer(intendedFertilizer) {
                let fertilizers = [];
                let isDuplicate = false;
                $("#table-fertilizer tbody tr").each(function() {
                    let fertilizer = $(this).find('select[name="fertilizer_name[]"]').val();

                    if (fertilizers.includes(intendedFertilizer)) {
                        isDuplicate = true;
                        return false;
                    }
                    fertilizers.push(fertilizer);
                });
                return isDuplicate;
            }

            $(document).on('change', '.fertilizer-select', function() {
                if (checkDuplicateFertilizer($(this).val())) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Pupuk Ganda',
                        text: 'Terdapat pupuk yang sama dalam rencana tanam',
                    });
                    $(this).val(null).trigger('change');
                    $(this).closest('tr').remove();
                } else {
                    let fertilizerId = $(this).val();
                    let farmerId = $("#id_master_farmer").val();
                    let closestTr = $(this).closest('tr');

                    $.ajax({
                        url: `{{ url('master-data/master-farmer/get-fertilizer-qty-owned') }}`,
                        type: 'GET',
                        data: {
                            fertilizer_id: fertilizerId,
                            farmer_id: farmerId
                        },
                        success: function(response) {
                            let quantityOwned = Number(response.quantity_owned ?? 0);
                            closestTr.find('#fertilizer_qty_owned').val(quantityOwned);
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                }
            });

            $('#land_area').on('input', function() {
                let landArea = $(this).val();
                if (landArea > maxLandArea) {
                    $(this).val('');
                    $(this).css('border', '1px solid red');
                    $("#max-land-area-text").css('color', 'red');
                } else {
                    $(this).css('border', '');
                    $("#max-land-area-text").css('color', '');
                }
            });

            $("#btnAddRow").click(function() {

                let idFarmer = $("#id_master_farmer").val();
                if (idFarmer == null || idFarmer == '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Pilih Nama Petani terlebih dahulu',
                    });
                    return;
                }

                let content = `
                    <tr>
                        <td>
                             <select name="fertilizer_name[]" class="form-control fertilizer-select" style="width=100%;" required>
                            </select>
                        </td>
                        <td><input type="text" name="fertilizer_qty_owned[]" id="fertilizer_qty_owned" class="form-control my-input-decimal" value="0" readonly></td>
                        <td><input type="text" name="fertilizer_qty_planned[]" id="fertilizer_qty_planned" class="form-control my-input-decimal" placeholder="Masukkan Qty Rencana" required></td>

                        <td><button class="btn btn-danger" id="btnDeleteRow"><i class="fas fa-trash"></i></button></td>
                    </tr>`;

                $("#table-fertilizer tbody").append(content);


                $(".fertilizer-select").last().select2({
                    ajax: {
                        url: "{{ url('resources/list-all-fertilizer') }}",
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
                    placeholder: 'Pilih Jenis Pupuk',
                    width: '100%'
                });
            });

            $("#table-fertilizer").on('click', '#btnDeleteRow', function() {
                $(this).closest('tr').remove();
            });


            $('#id_master_farmer').select2({
                ajax: {
                    url: "{{ url('resources/list-all-farmer') }}",
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
                placeholder: 'Pilih Nama Petani',
                minimumInputLength: 0,
                width: '100%'

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
            $(document).on('input', '.my-input-decimal', function() {
                this.value = this.value.replace(/[^0-9.]/g, '');

            })
        });
    </script>
@stop
