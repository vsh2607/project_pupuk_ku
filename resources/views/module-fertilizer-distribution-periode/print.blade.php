<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Distribusi Periode {{ $data->periode }}</title>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>


</head>

<body>
    <table style="text-align: center">
        <tr>
            <th style="text-align: center">DAFTAR DISTRIBUSI PUPUK PERIODE {{ $data->periode }} / {{ $data->periode_date }}</th>
        </tr>
    </table>

    <br>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="text-align: center">Nama Peminjam</th>
                <th style="text-align: center">Nama Pemberi</th>
                <th style="width: 5%; text-align:center;">Total Pinjaman</th>
                <th style="text-align: center">TTD Peminjam</th>
                <th style="text-align: center">TTD Pemberi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->tdFertilizerDistribution as $key => $item)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $item->farmerBorrower->name }}</td>
                    <td>{{ $item->farmerLender->name }}</td>
                    <td style="text-align: end">{{ intval($item->total_loan) }} KG</td>
                    <td style="height: 50px;"></td>
                    <td></td>
                </tr>
            @endforeach

        </tbody>
    </table>
</body>

</html>
