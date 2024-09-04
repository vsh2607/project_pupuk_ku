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
                        $lenderIds = $model->farmerBorrower->pluck('id_farmer_lender')->unique()->toArray();
                        $lenderIdsString = implode(',', $lenderIds);
                        return '<a href="#" class="open-dialog" id="borrowButtonModal" data-borrower-id="' . $model->id . '" data-lender-ids="' . $lenderIdsString . '">Masih ada Pinjaman</a>';
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


    public function listLenderLended(Request $request)
    {

        $borrowerId = $request->borrowerId;
        $lenderIds = explode(',', $request->lenderIds);
        $loanData = TDFertilizerDistribution::with([
            'farmerBorrower' => function ($query) {
                $query->select('id', 'name as borrower_name');
            },
            'farmerLender' => function ($query) {
                $query->select('id', 'name as lender_name');
            }
        ])
            ->where('id_farmer_borrower', $borrowerId)
            ->whereIn('id_farmer_lender', $lenderIds)
            ->whereRaw('total_loan - total_return > 0')
            ->select('id', 'total_loan', 'total_return', 'created_at', 'id_farmer_borrower', 'id_farmer_lender')
            ->get();


        return response()->json($loanData);
    }

    public function updateLoan(Request $request)
    {
        DB::beginTransaction();
        try {
            $distributionData = TDFertilizerDistribution::find($request->distribution_id);
            $distributionData->update([
                'total_return' => $request->total_returned + $distributionData->total_return
            ]);
            DB::commit();
            return response()->json(['message' => 'Pinjaman Berhasil Dikembalikan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
