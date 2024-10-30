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

        .center {
            text-align: center;
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
                {{-- <button class="btn btn-info btn-md btn-show-map float-right" style="margin-right: 10px" type="button"><i
                        class="fas fa-map"></i>
                    Tampilkan Peta</button> --}}
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
                                <th>Jumlah Pupuk Kepemilikan</th>
                                <th>Info Rencana Tanam</th>
                                <th>Info Pinjaman</th>
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


    <div class="modal fade" role="dialog" id="hasPlantingPlanModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Daftar Rencana Tanam</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table" id="hasPlantingTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th class="center">Kode Rencana</th>
                                    <th class="center">Tanggal Perencanaan</th>
                                    <th class="center">Jumlah Lahan Recana</th>
                                    <th class="center">Jumlah Kepemilikan Pupuk</th>
                                    <th class="center">Jumlah Kebutuhan Pupuk</th>
                                    <th class="center">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>



    <div class="modal fade" role="dialog" id="hasBorrowedModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Daftar Pinjaman</h5>
                    <button type="button" class="close" id="hasBorrowedBtnModalClose" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table" id="hasBorrowedTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th class="center">Kode Rencana</th>
                                    <th class="center">Nama Pemberi</th>
                                    <th class="center">Pupuk</th>
                                    <th class="center">Sisa Pinjaman</th>
                                    <th class="center">Tanggal Pinjaman</th>
                                    <th class="center">Jumlah Pengembalian</th>
                                    <th class="center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>








@stop

@section('plugins.Sweetalert2', true)
@section('plugins.Datatables', true)
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/leafletjs/leaflet.js"></script>

    <script>
        $(document).ready(function() {


            $(document).on("click", "#hasBorrowedBtnModal", function() {
                let farmerBorrowerId = $(this).data("id");
                $("#hasBorrowedModal").modal("show");

                $("#hasBorrowedTable").DataTable().clear().destroy();

                $("#hasBorrowedTable").DataTable({
                    proccessing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ url('resources/list-farmer-borrower') }}",
                        data: {
                            farmerBorrowerId: farmerBorrowerId
                        },
                        dataSrc: function(json) {
                            if (json.data.length === 0) {
                                window.location.reload();
                            }
                            return json.data;
                        }
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
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'lender_name',
                            name: 'lender_name'
                        },
                        {
                            data: 'fertilizer_name',
                            name: 'fertilizer_name'
                        },
                        {
                            data: 'loan_remainder',
                            name: 'loan_remainder',
                        },
                        {
                            data: 'loan_date',
                            name: 'loan_date',
                        },
                        {
                            data: 'qty_return',
                            name: 'qty_return',
                        },
                        {
                            data: 'action',
                            name: 'action'
                        }

                    ]

                });


            });

            $(document).on("click", "#hasPlantingPlanBtnModal", function() {
                let farmerBorrowerId = $(this).data("id");
                $("#hasPlantingPlanModal").modal("show");

                $("#hasPlantingTable").DataTable().clear().destroy();

                $("#hasPlantingTable").DataTable({
                    proccessing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ url('resources/list-planting-plan') }}",
                        data: {
                            farmerBorrowerId: farmerBorrowerId
                        }
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
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'planned_date',
                            name: 'planned_date'
                        },
                        {
                            data: 'land_area',
                            name: 'land_area'
                        },
                        {
                            data: 'fertilizers_owned',
                            name: 'fertilizers_owned',
                        },
                        {
                            data: 'fertilizers_planned',
                            name: 'fertilizers_planned',
                        },
                        {
                            data: 'info',
                            name: 'info'
                        }



                    ]



                })

                // $.ajax({
                //     type: 'GET',
                //     url: "{{ url('resources/list-planting-plan') }}",
                //     data: {
                //         farmerBorrowerId: farmerBorrowerId
                //     },
                //     success: function(response) {
                //         console.log(response);
                //         let tr = '';
                //         response.forEach(function(e) {
                //             tr += '<tr>';
                //             tr += '<td>' + e.code + '</td>';
                //             tr += '<td class="center">' + e.planned_date + '</td>';
                //             tr += '<td class="center">' + e.land_area + ' m<sup>2</sup></td>';
                //             tr += '<td class="" style="width:40%">' +
                //                 '<ul>';
                //             e.master_farmer.master_farmer_fertilizer.forEach(function(fertilizer) {
                //                 tr += '<li style="white-space: nowrap;">' +
                //                     fertilizer.master_fertilizer.name + ': ' +
                //                     fertilizer.quantity_owned + ' KG </li>';
                //             });
                //             tr += '</ul>'; +
                //             '</td>';
                //             tr += '<td class="" style="width:40%">' +
                //                 '<ul>';
                //             e.t_d_farmer_planned.forEach(function(fertilizer) {
                //                 tr += '<li style="white-space: nowrap;">' +
                //                     fertilizer.master_fertilizer.name + ': ' +
                //                     fertilizer.quantity_planned + ' KG </li>';
                //             });
                //             tr += '</ul>'; +
                //             '</td>';
                //             tr += '<td class="center"></td>';
                //             tr += '</tr>';
                //         });

                //         $("#hasPlantingTable tbody").append(tr);


                //     },
                //     error: function(err) {
                //         console.log(err);
                //     }
                // })

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
                        },
                    },

                    {
                        data: 'farmer_name',
                        name: 'farmer_name'
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
                        data: 'fertilizer_owned',
                        name: 'fertilizer_owned'
                    },
                    {
                        data: 'planting_plan_status',
                        name: 'planting_plan_status'
                    },
                    {
                        data: 'borrow_status',
                        name: 'borrow_status'
                    }

                ]

            });

            $(document).on("click", "#returnLoanBtn", function() {
                let distributionId = $(this).data("id");
                let qty_return = parseFloat($(this).closest("tr").find("input[name='qty_return']").val());
                let max = parseFloat($(this).closest("tr").find("input[name='qty_return']").attr("max"));

                if (qty_return < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Total Tidak Boleh Kurang dari 0',
                        timer: 2000,
                        showConfirmButton: false,
                    })
                    return ;
                } else if (qty_return > max) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Total Tidak Boleh Lebih dari Pinjaman',
                        timer: 2000,
                        showConfirmButton: false,
                    })
                    return ;
                } else if (qty_return == 0 || qty_return === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Total Tidak Boleh Kosong',
                        timer: 2000,
                        showConfirmButton: false,
                    })
                    return ;
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ url('module-management/fertilizer-distribution/update-loan') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        distributionId: distributionId,
                        qty_return: qty_return
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false,
                        })

                        $("#hasBorrowedTable").DataTable().ajax.reload();
                    },
                    error: function(err) {
                        console.log(err);
                    }
                })

            });


            $("#hasBorrowedBtnModalClose").click(function() {
                window.location.reload();
            });

            $('#hasBorrowedModal').on('hidden.bs.modal', function () {
                window.location.reload();
            });

        });
    </script>

    {{-- <script>
        $(document).ready(function() {


            function updateTableAjax(lenderIds, borrowerId) {
                $.ajax({
                    method: 'POST',
                    url: "{{ url('/resources/list-lender-lended/') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        borrowerId: borrowerId,
                        lenderIds: lenderIds,
                    },
                    success: function(response) {
                        console.log(response.length);
                        if (response.length == 0) {
                            window.location.reload();
                            return;
                        }

                        let tbody = '';
                        response.forEach(function(item) {
                            tbody += '<tr>';
                            tbody += '<td>' + item.farmer_borrower.borrower_name +
                                '</td>';
                            tbody += '<td>' + item.farmer_lender.lender_name + '</td>';
                            tbody += '<td>' + (parseFloat(item.total_loan) - parseFloat(
                                item.total_return)) + ' KG</td>';
                            tbody += '<td>' + item.created_at.split('T')[0] + '</td>';
                            tbody +=
                                '<td><input type="number" class="form-control" name="total_returned" min="0" max="' +
                                (parseFloat(item.total_loan) - parseFloat(
                                    item.total_return)) + '"></td>';
                            tbody +=
                                '<td><button type="button" class="btn btn-primary save-return" data-total-loan="'+(parseFloat(item.total_loan) - parseFloat(item.total_return))+'" data-id="' + item.id + '" data-lender-ids="' + lenderIds + '" data-borrower-id="' + borrowerId +'">Save</button></td>';
                            tbody += '</tr>';
                        });
                        $('#myPinjamanTable tbody').append(tbody);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            $(document).on('click', '#borrowButtonModal', function() {
                $('#myPinjamanTable tbody').empty();
                let borrowerId = $(this).data('borrower-id');
                let lenderIds = $(this).data('lender-ids');
                $("#borrowModal").modal('show');

                updateTableAjax(lenderIds, borrowerId);

            });

            $(document).on('click', '.save-return', function() {
                let total_returned = $(this).closest('tr').find('input[name="total_returned"]').val();
                let distribution_id = $(this).data('id');
                let borrowerId = $(this).data('borrower-id');
                let lenderIds = $(this).data('lender-ids');
                let total_loan = $(this).data('total-loan');


                if(total_returned < 0){
                    alert("Total Tidak Boleh Kurang dari 0");
                    return;
                }else if(total_returned > total_loan){
                    alert("Total Tidak Boleh Lebih dari Pinjaman");
                    return;
                }else if(total_returned == 0 || total_returned === ''){
                    alert("Total Tidak Boleh Kosong");
                    return;
                }


                $.ajax({
                    method: 'POST',
                    url: "{{ url('/module-management/fertilizer-distribution/update-loan') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        total_returned: total_returned,
                        distribution_id: distribution_id
                    },
                    success: function(response) {
                        $('#successAlert').text(response.message).removeClass('d-none');
                        setTimeout(function() {
                            $('#successAlert').addClass('d-none');
                        }, 2000);
                        $('#myPinjamanTable tbody').empty();
                        updateTableAjax(lenderIds, borrowerId);
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                        $('#errorAlert').text(response.error).removeClass('d-none');
                        setTimeout(function() {
                            $('#errorAlert').addClass('d-none');
                        }, 2000);
                    }
                });

            })

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
                        data: 'totalLended',
                        name: 'totalLended',
                    },
                    {
                        data: 'totalBorrowed',
                        name: 'totalBorrowed',
                    },

                    {
                        data: 'fertilizer_quantity_remainder',
                        name: 'fertilizer_quantity_remainder'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'loan_status',
                        name: 'loan_status'
                    }

                ]

            });
        })
    </script> --}}
@stop
