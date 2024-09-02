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
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="required" for="name">Nama Tanaman</label>
                                <input type="text" required name="name" id="name" class="form-control my-input"
                                    placeholder="Nama Tanaman" value="{{ $data->name }}">
                            </div>
                        </div>

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
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {

            $(".my-input").on('input', function() {
                $(this).val($(this).val().toUpperCase());
            })
        });
    </script>
@stop
