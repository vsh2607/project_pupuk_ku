<?php

namespace App\Http\Controllers;

use App\Models\MasterFarmer;
use App\Models\TDFertilizerDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\THFertilizerDistribution;
use Yajra\DataTables\Facades\DataTables;

class ModuleFertilizerDistributionController extends Controller
{
    public function index()
    {
        $mapData = MasterFarmer::select('land_area', 'land_location', 'name', 'fertilizer_quantity_needed', 'fertilizer_quantity_owned')->get();


        return view('module-fertilizer-distribution.index', ['mapData' => $mapData]);
    }

    public function listData(Request $request)
    {
        $model = MasterFarmer::with(['farmerPlants', 'farmerPlants.plant', 'farmerLender', 'farmerBorrower'])
            ->select('master_farmers.*')
            ->addSelect(DB::raw('(SELECT SUM(total_loan - total_return) FROM td_fertilizer_distribution WHERE id_farmer_lender = master_farmers.id) AS total_lended'))
            ->addSelect(DB::raw('(SELECT SUM(total_loan - total_return) FROM td_fertilizer_distribution WHERE id_farmer_borrower = master_farmers.id) AS total_borrowed'));

        return DataTables::of($model)
            ->addColumn('loan_status', function ($model) {
                if ($model->total_borrowed != null) {
                    if ($model->total_borrowed > 0) {
                        return '<a href="#" class="open-dialog" data-message="Masih ada Pinjaman">Masih ada Pinjaman</a>';
                    }
                }
                return '-';
            })
            ->addColumn('totalLended', function ($model) {
                return $model->total_lended != null ? $model->total_lended . " KG" : '0 KG';
            })
            ->addColumn('totalBorrowed', function ($model) {
                return $model->total_borrowed != null ? $model->total_borrowed . " KG" : '0 KG';
            })
            ->editColumn('land_area', function ($model) {
                return $model->land_area . ' m<sup>2</sup>';
            })
            ->editColumn('fertilizer_quantity_owned', function ($model) {
                return $model->fertilizer_quantity_owned . ' KG';
            })
            ->editColumn('fertilizer_quantity_needed', function ($model) {
                return $model->fertilizer_quantity_needed . ' KG';
            })
            ->editColumn('land_type', function ($model) {
                return $model->land_type == "OWNED" ? "Punya Sendiri" : "Garap Orang Lain";
            })
            ->addColumn('plant_type', function ($model) {
                $plantTypes = '';
                $plantTypes = '<ul>';
                foreach ($model->farmerPlants as $plantType) {
                    $plantTypes .= '<li>' . $plantType->plant->name . '</li>';
                }
                $plantTypes .= '</ul>';
                return $plantTypes;
            })
            ->addColumn('fertilizer_quantity_remainder', function ($model) {
                $fertilizer_quantity_remainder = ($model->fertilizer_quantity_owned + $model->total_borrowed ?? 0)  - ($model->fertilizer_quantity_needed + $model->total_lended ?? 0);
                return $fertilizer_quantity_remainder . ' KG';
            })
            ->addColumn('status', function ($model) {
                $fertilizer_quantity_remainder = ($model->fertilizer_quantity_owned + $model->total_borrowed ?? 0)  - ($model->fertilizer_quantity_needed + $model->total_lended ?? 0);
                if ($fertilizer_quantity_remainder > 0) {
                    return '<span class="text-success">Berlebih</span>';
                } elseif ($fertilizer_quantity_remainder < 0) {
                    return '<span class="text-danger">Kekurangan</span>';
                } else {
                    return '<span class="text-secondary">Cukup</span>';
                }
            })
            ->addColumn('action', function ($model) {
                $infoButton = "&nbsp;<a href='" . url('master-data/master-farmer/' . $model->id . '/info') . "' class='btn  btn-primary d-inline-block'><i class='fas fa-info'></i></a>";
                $editButton = "&nbsp;<a href='" . url('master-data/master-farmer/' . $model->id . '/edit') . "' class='btn  btn-warning d-inline-block'><i class='fas fa-edit'></i></a>";
                $deleteButton = "&nbsp;<a href='#' class='btn  btn-danger d-inline-block'><i class='fas fa-trash'></i></a>";
                $mapButton = '<button class="btn btn-success d-inline-block btn-map" type="button" data-location="' . $model->land_location . '"><i class="fas fa-map-marked-alt"></i></button>';
                return  $mapButton . $infoButton . $editButton;
            })
            ->rawColumns(['action', 'land_area', 'land_type', 'plant_type', 'status', 'loan_status'])
            ->toJson();
    }


    public function addForm()
    {
        $totalPeriodeDistribution = THFertilizerDistribution::count();
        return view('module-fertilizer-distribution.add', ["totalPeriodeDistribution" => $totalPeriodeDistribution]);
    }

    public function addData(Request $request)
    {

        DB::beginTransaction();

        try {
            $totalPeriodeDistribution = THFertilizerDistribution::count();
            $thFertilizerDistribution = THFertilizerDistribution::updateOrCreate([
                'periode' => ++$totalPeriodeDistribution,
                'periode_date' => $request->periode_date
            ]);

            foreach ($request->borrower_name as $key => $val) {

                TDFertilizerDistribution::create([
                    'id_th_fertilizer_distribution' => $thFertilizerDistribution->id,
                    'id_farmer_borrower' => $val,
                    'id_farmer_lender' => $request->lender_name[$key],
                    'total_loan' => $request->total_loan[$key]
                ]);
            }

            DB::commit();
            return redirect('/module-management/fertilizer-distribution')->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/module-management/fertilizer-distribution')->with('error', $e->getMessage());
        }
    }
}
