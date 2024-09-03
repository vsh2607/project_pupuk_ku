@extends('adminlte::page')

@section('title', 'Tambah Data Petani')

@section('adminlte_css_pre')
    <link rel="icon" href="{{ asset('assets/logo.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .required::after {
            content: '*';
            color: red;
        }
    </style>
@endsection

@section('content_header')
    <h1>Tambah Data Distribusi</h1>
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
                                <label class="required" for="periode">Periode</label>
                                <input type="text" required name="periode" id="periode" class="form-control my-input"
                                    value="PERIODE {{ ++$totalPeriodeDistribution }}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="required" for="periode_date">Tanggal</label>
                                <input type="date" required name="periode_date" id="periode_date" class="form-control my-input"
                                    placeholder="Masukkan Tanggal" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                        </div>

                    </div>

                </div>

                <div class="card-body">
                    <p>DAFTAR DISTRIBUSI PUPUK</p>
                    <div class="table-responsive" style="overflow-x: auto;">

                        <table style="width: 100%" class="table" id="myDistTable">
                            <thead>
                                <tr>
                                    <td>Nama Petani (Peminjam)</td>
                                    <td>Nama Petani (Pemberi)</td>
                                    <td>Total Pinjaman</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <button style="width: 100%; margin: 0 auto;" type="button"
                        class="btn btn-success btn-add-row">TAMBAH</button>
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
    <script>
        $(document).ready(function() {




            function getCurrentBorrowedTotal(borrowerId) {
                let totalBorrowed = 0;
                $("select.borrower_name").each(function() {
                    if ($(this).val() === borrowerId) {
                        let loan = parseFloat($(this).closest('tr').find('.total_loan').val());
                        if (!isNaN(loan)) {
                            totalBorrowed += loan;
                        }
                    }
                });

                return totalBorrowed;
            }

            function getCurrentLendedTotal(lenderId) {
                let totalLended = 0;
                $("select.lender_name").each(function() {
                    if ($(this).val() === lenderId) {
                        let loan = parseFloat($(this).closest('tr').find('.total_loan').val());
                        if (!isNaN(loan)) {
                            totalLended += loan;
                        }
                    }
                });

                return totalLended;
            }



            function borrowerNameSelect2() {
                $(".borrower_name").select2({
                    ignore: [],
                    ajax: {
                        url: '{{ url('resources/list-borrower-candidates') }}',
                        data: function(params) {
                            var query = {
                                name: params.term
                            }
                            return query;
                        },
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            var data = $.map(data, function(obj) {
                                obj.id = obj.id;
                                obj.text = obj.name;
                                obj.max = obj.max
                                return obj;
                            });
                            return {
                                results: data,
                            };
                        },
                    },
                    minimumInputLength: 0,
                    placeholder: 'CARI NAMA PEMINJAM',
                });

            }

            function lenderNameSelect2() {
                $(".lender_name").select2({
                    ignore: [],
                    ajax: {
                        url: '{{ url('resources/list-lender-candidates') }}',
                        data: function(params) {
                            var query = {
                                name: params.term
                            }
                            return query;
                        },
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            var data = $.map(data, function(obj) {
                                obj.id = obj.id;
                                obj.text = obj.name;
                                obj.max = obj.max
                                return obj;
                            });
                            return {
                                results: data,
                            };
                        },
                    },
                    minimumInputLength: 0,
                    placeholder: 'CARI NAMA PEMBERI',
                });

            }

            borrowerNameSelect2();
            lenderNameSelect2();
            $("form").submit(function(e) {

                if ($('.added-row').length === 0) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Error',
                        text: 'Data Distribusi Tidak Boleh Kosong',

                    });
                }

                let isValid = true;

                $('.added-row').each(function() {
                    $(this).find(
                        'select, input[type="text"], input[type="number"]'
                    ).each(
                        function() {
                            let value = $(this).val();

                            if ($(this).hasClass('select2-hidden-accessible')) {
                                value = $(this).select2('val');
                            }
                            if (!value) {
                                isValid = false;

                                $(this).next('.select2').find('.select2-selection').css(
                                    'border',
                                    '2px solid red');
                                $(this).css('border', '2px solid red');
                            } else {

                                $(this).next('.select2').find('.select2-selection').css(
                                    'border', '');
                                $(this).css('border', '');
                            }
                        });
                });

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error',
                        text: 'Detail Distribusi Tidak Boleh Kosong',

                    });
                }
            });


            $(".btn-add-row").click(function() {
                let newRow = `<tr class='added-row'>
                                <td style="width: 35%;">
                                    <select name="borrower_name[]" class="form-control borrower_name">
                                    </select>
                                </td>
                                <td style="width: 35%;">
                                    <select name="lender_name[]" class="form-control lender_name">
                                    </select>
                                </td>
                                <td style="width: 20%;"><input type="number" name="total_loan[]" class="form-control total_loan" placeholder="Total" step="0.1"></td>
                                <td style="width: 10%;"><button type="button" class="btn btn-danger btn-remove-row"><i class="fas fa-trash"></i></button></td>

                              </tr>`;
                $("#myDistTable").append(newRow);
                borrowerNameSelect2();
                lenderNameSelect2();

                $(".borrower_name").on("change", function() {
                    let inputTotalLoan = $(this).closest("tr").find(".total_loan");
                    inputTotalLoan.val(0);
                    let name = $(this).select2('data').map(function(option) {
                        return option.name.replace(/\s*\(.*\)/, '');
                    })
                    let maxNeeded = $(this).select2('data').map(function(option) {
                        return Math.abs(option.max);
                    });
                    maxNeeded = maxNeeded[0];
                    let borrowerId = $(this).val();
                    let totalBorrowed = getCurrentBorrowedTotal(borrowerId);


                    //For Lender
                    let lender = $(this).closest("tr").find(".lender_name");
                    let lender_id = lender.val();
                    let lenderMaxLended = lender.select2('data').map(function(option) {
                        return Math.abs(option.max);
                    });

                    lenderMaxLended = lenderMaxLended[0];
                    let lenderTotalLended = getCurrentLendedTotal(lender_id);

                    let remainderBorrowed = maxNeeded - totalBorrowed;
                    let remainderLended = lenderMaxLended - lenderTotalLended;

                    if (remainderBorrowed <= remainderLended) {
                        inputTotalLoan.attr('max', remainderBorrowed)
                        inputTotalLoan.val(remainderBorrowed)
                    } else {
                        inputTotalLoan.attr('max', remainderLended);
                        inputTotalLoan.val(remainderLended);
                    }

                    if (totalBorrowed >= maxNeeded) {
                        Swal.fire({
                            title: 'Info',
                            text: `Pinjaman Petani ${name} Sudah Sesuai Yang Dibutuhkan, yaitu ${maxNeeded} KG`,

                        });
                        $(this).closest('tr').remove();
                    }


                });

                $(".lender_name").on("change", function() {
                    let name = $(this).select2('data').map(function(option) {
                        return option.name.replace(/\s*\(.*\)/, '');
                    })
                    let inputTotalLoan = $(this).closest("tr").find(".total_loan");
                    inputTotalLoan.val(0);
                    let maxLended = $(this).select2('data').map(function(option) {
                        return Math.abs(option.max);
                    });
                    maxLended = maxLended[0];
                    let lenderId = $(this).val();
                    let totalLended = getCurrentLendedTotal(lenderId);

                    //For Borrower
                    let borrower = $(this).closest("tr").find(".borrower_name");
                    let borrower_id = borrower.val();
                    let borrowerMaxBorrowed = borrower.select2('data').map(function(option) {
                        return Math.abs(option.max);
                    });

                    borrowerMaxBorrowed = borrowerMaxBorrowed[0];
                    let borrowerTotalBorrowed = getCurrentBorrowedTotal(borrower_id);


                    let remainderLended = maxLended - totalLended;
                    let remainderBorrowed = borrowerMaxBorrowed - borrowerTotalBorrowed;

                    if (remainderBorrowed <= remainderLended) {
                        inputTotalLoan.attr('max', remainderBorrowed)
                        inputTotalLoan.val(remainderBorrowed)
                    } else {
                        inputTotalLoan.attr('max', remainderLended);
                        inputTotalLoan.val(remainderLended);
                    }


                    if (totalLended >= maxLended) {
                        Swal.fire({
                            title: 'Info',
                            text: `Petani ${name} Sudah Meminjamkan Semua Dari Yang Dimiliki, yaitu ${maxLended} KG`,

                        });
                        $(this).closest('tr').remove();
                    }
                });


            });



            $(document).on('click', '.btn-remove-row', function() {
                $(this).closest('tr').remove();
            });

            $(".my-input").on('input', function() {
                $(this).val($(this).val().toUpperCase());
            })
        });
    </script>
@stop
