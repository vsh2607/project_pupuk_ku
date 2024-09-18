<?php

namespace App\Http\Controllers;

use App\Models\MasterFarmer;
use App\Models\TDFertilizerDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\THFertilizerDistribution;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class ModuleFertilizerDistributionController extends Controller
{
    public function index()
    {
        $mapData = MasterFarmer::select('land_area', 'land_location', 'name', 'fertilizer_quantity_needed', 'fertilizer_quantity_owned')->get();


        return view('module-fertilizer-distribution.index', ['mapData' => $mapData]);
    }


    public function indexPeriode()
    {
        return view('module-fertilizer-distribution-periode.index');
    }

    public function listDataPeriode(Request $request)
    {
        $model = THFertilizerDistribution::with(['tdFertilizerDistribution']);
        return DataTables::of($model)
            ->editColumn('periode', function ($model) {
                return 'PERIODE - ' . $model->periode;
            })
            ->addColumn('action', function ($model) {
                $editButton = "&nbsp;<a href='" . url('module-management/fertilizer-distribution-periode/' . $model->id . '/edit') . "' class='btn btn-warning'><i class='fas fa-edit'></i></a>";
                $infoButton = "<a href='" . url('module-management/fertilizer-distribution-periode/' . $model->id . '/info') . "' class='btn btn-primary'><i class='fas fa-info'></i></a>";
                $printButton = "<a href='" . url('module-management/fertilizer-distribution-periode/' . $model->id . '/print') . "' class='btn btn-primary'><i class='fas fa-print'></i></a>";
                return $printButton;
            })
            ->rawColumns(['action'])
            ->toJson();
    }


    public function printData($id)
    {
        $data = THFertilizerDistribution::with(['tdFertilizerDistribution', 'tdFertilizerDistribution.farmerBorrower', 'tdFertilizerDistribution.farmerLender'])->where('id', $id)->first();
        $pdf = Pdf::loadView('module-fertilizer-distribution-periode.print', ['data' => $data]);
        return $pdf->stream('test.pdf');
    }

    public function editForm($id)
    {
        // $data = THFertilizerDistribution::with(['tdFertilizerDistribution', 'tdFertilizerDistribution.farmerBorrower', 'tdFertilizerDistribution.farmerLender'])->first();

        $data = THFertilizerDistribution::join('td_fertilizer_distribution as tfd', 'th_fertilizer_distribution.id', '=', 'tfd.id_th_fertilizer_distribution')
            ->join('master_farmers as mf_borrower', 'tfd.id_farmer_borrower', 'mf_borrower.id')
            ->join('master_farmers as mf_lender', 'tfd.id_farmer_lender', 'mf_lender.id')
            ->where('th_fertilizer_distribution.id', $id)
            ->select('th_fertilizer_distribution.id', 'mf_borrower.name AS borrower_name', 'mf_lender.name AS lender_name', 'tfd.total_loan', 'mf_borrower.id AS borrower_id', 'mf_lender.id AS lender_id')
            ->addSelect(DB::raw('(SELECT COALESCE(SUM(total_loan - total_return), 0) FROM td_fertilizer_distribution WHERE id_farmer_borrower = mf_borrower.id) AS total_borrowed'))
            ->addSelect(DB::raw("CONCAT(mf_borrower.name, ' (', (fertilizer_quantity_owned - fertilizer_quantity_needed) + (SELECT COALESCE(SUM(total_loan - total_return), 0) FROM td_fertilizer_distribution WHERE id_farmer_borrower = mf_borrower.id), ' KG)') as borrower_name_alias"))
            ->addSelect(DB::raw('(fertilizer_quantity_owned - fertilizer_quantity_needed) + (SELECT COALESCE(SUM(total_loan - total_return), 0) FROM td_fertilizer_distribution WHERE id_farmer_borrower = mf_borrower.id) as borrower_max'))
            ->get();

        dd($data);


        // $data = MasterFarmer::with(['farmerBorrower'])->where('name', 'LIKE', '%' . $search_word . '%')
        //     ->select('id', DB::raw("CONCAT(name, ' (', (fertilizer_quantity_owned - fertilizer_quantity_needed) + (SELECT COALESCE(SUM(total_loan - total_return), 0) FROM td_fertilizer_distribution WHERE id_farmer_borrower = master_farmers.id), ' KG)') as name"), DB::raw('(fertilizer_quantity_owned - fertilizer_quantity_needed) + (SELECT COALESCE(SUM(total_loan - total_return), 0) FROM td_fertilizer_distribution WHERE id_farmer_borrower = master_farmers.id) as max'), 'fertilizer_quantity_owned AS fqo', 'fertilizer_quantity_needed AS fqn')
        //     ->addSelect(DB::raw('(SELECT COALESCE(SUM(total_loan - total_return), 0) FROM td_fertilizer_distribution WHERE id_farmer_borrower = master_farmers.id) AS total_borrowed'))
        //     ->having('total_borrowed', '=', 0)
        //     ->havingRaw('fqo + total_borrowed - fqn < 0')
        //     ->get();


        // dd($data);
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
            return redirect('/module-management/fertilizer-distribution-periode')->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/module-management/fertilizer-distribution-periode')->with('error', $e->getMessage());
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
