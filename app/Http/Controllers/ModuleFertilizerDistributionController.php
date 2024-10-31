<?php

namespace App\Http\Controllers;

use App\Models\MasterFarmer;
use App\Models\TDFarmerPlanned;
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
        // $mapData = MasterFarmer::select('land_area', 'land_location', 'name', 'fertilizer_quantity_needed', 'fertilizer_quantity_owned')->get();
        return view('module-fertilizer-distribution.index');
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
        $data = THFertilizerDistribution::with(['tdFertilizerDistribution.MasterFertilizer', 'tdFertilizerDistribution.farmerBorrower', 'tdFertilizerDistribution.farmerLender'])->where('id', $id)->first();
        $pdf = Pdf::loadView('module-fertilizer-distribution-periode.print', ['data' => $data]);
        return $pdf->stream('test.pdf');
    }

    public function editForm($id)
    {

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


        $model = MasterFarmer::with([
            "MasterFarmerFertilizer.MasterFertilizer",
            "THFarmerPlanned.TDFarmerPlanned",
            "THFarmerPlanned.TDFarmerPlantPlanned.MasterPlant",
            "TDFarmerBorrowerDistribution"
        ])
            ->get();


        return DataTables::of($model)
            ->addColumn("farmer_name", function ($model) {
                return $model->name;
            })
            ->addColumn("borrow_status", function ($model) {
                $bor = $model->TDFarmerBorrowerDistribution;
                if ($bor->isEmpty() || $bor->sum("total_loan") - $bor->sum("total_return") <= 0) {
                    return "<span>Tidak Ada Pinjaman</span>";
                } else {
                    return '<a href="#" class="open-dialog-has-borrow" id="hasBorrowedBtnModal" data-id="' . $model->id . '">Ada Pinjaman</a>';
                }
            })
            ->addColumn("planting_plan_status", function ($model) {
                $plan = $model->THFarmerPlanned->filter(function ($plan) {
                    return $plan->status == 0;
                });

                if ($plan->isEmpty()) {
                    return "<span>Tidak Ada Rencana Tanam</span>";
                } else {
                    return '<a href="#" class="open-dialog-has-plan" id="hasPlantingPlanBtnModal" data-id="' . $model->id . '">Ada Rencana Tanam</a>';
                }
            })
            ->editColumn('land_area', function ($model) {
                return $model->land_area . ' m<sup>2</sup>';
            })
            ->editColumn('land_type', function ($model) {
                return $model->land_type == "OWNED" ? "Punya Sendiri" : "Garap Orang Lain";
            })
            ->addColumn('fertilizer_owned', function ($model) {
                $fertilizers = $model->MasterFarmerFertilizer;
                $fertilizerString = "<ul>";
                foreach ($fertilizers as $fertilizer) {
                    $fertilizerString .= "<li>" . $fertilizer->MasterFertilizer->name . " : " . number_format($fertilizer->quantity_owned, 2, ',', '.') . " KG</li>";
                }
                $fertilizerString .= "</ul>";

                return $fertilizerString;
            })

            ->rawColumns(["borrow_status", 'land_area', 'fertilizer_owned', 'land_type', 'planting_plan_status', 'borrow_status'])
            ->toJson();



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
                'periode_date_start' => $request->periode_date_start,
                'periode_date_end' => $request->periode_date_end

            ]);

            foreach ($request->borrower_id as $key => $val) {

                TDFertilizerDistribution::create([
                    'id_th_fertilizer_distribution' => $thFertilizerDistribution->id,
                    'id_farmer_borrower' => $val,
                    'id_farmer_lender' => $request->lender_id[$key],
                    'id_th_farmer_planned' => $request->th_farmer_planned_id[$key],
                    'total_loan' => $request->total_loan[$key],
                    'id_master_fertilizer' => $request->master_fertilizer_id[$key]
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
            $distributionData = TDFertilizerDistribution::find($request->distributionId);
            $distributionData->update([
                'total_return' => $request->qty_return + $distributionData->total_return
            ]);
            DB::commit();
            return response()->json(['message' => 'Pinjaman Berhasil Dikembalikan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getTotalCurrentLent($array, $id, $id_fertilizer)
    {

        try {
            $totalSum = array_sum(
                array_map(
                    function ($item) {
                        return $item['total_lent'];
                    },
                    array_filter($array, function ($item) use ($id, $id_fertilizer) {
                        return $item['id_farmer_lender'] === $id && $item['id_fertilizer'] === $id_fertilizer;
                    })
                )
            );
            return $totalSum;
        } catch (\Exception $e) {
            return 0;
        }
    }




    public function getFarmerFertilizerNeeded(Request $request)
    {
        $needs = TDFarmerPlanned::join("th_farmer_planned", "td_farmer_planned.id_th_farmer_planned", "=", "th_farmer_planned.id")
            ->join("master_fertilizers", "td_farmer_planned.id_master_fertilizer", "=", "master_fertilizers.id")
            ->join("master_farmers", "th_farmer_planned.id_master_farmer", "=", "master_farmers.id")
            // ->leftJoin("td_fertilizer_distribution", "td_fertilizer_distribution.id_farmer_borrower", "=", "master_farmers.id")
            // ->whereRaw("td_fertilizer_distribution.total_loan = td_fertilizer_distribution.total_return")
            // // ->whereNull("td_fertilizer_distribution.id")
            ->where("th_farmer_planned.status", 0)
            ->whereBetween("th_farmer_planned.planned_date", [$request->start_date, $request->end_date])
            ->whereRaw("td_farmer_planned.quantity_planned > td_farmer_planned.quantity_owned")
            ->select("th_farmer_planned.code", "master_farmers.id as farmer_id", "master_farmers.name as farmer_name", "master_fertilizers.id as fertilizer_id", "master_fertilizers.name as fertilizer_name", "td_farmer_planned.quantity_owned", "td_farmer_planned.quantity_planned", "th_farmer_planned.id as id_planning")
            ->addSelect(DB::raw('(SELECT COALESCE(SUM(total_loan - total_return), 0) FROM td_fertilizer_distribution WHERE id_farmer_borrower = master_farmers.id AND id_master_fertilizer) AS total_borrowed'))
            ->having('total_borrowed', '=', 0)
            ->orderBy("td_farmer_planned.quantity_owned", "ASC")
            ->orderBy("td_farmer_planned.quantity_planned", "ASC")
            ->get();

        $tracking_lent_arr = [];


        // dd($needs);


        foreach ($needs as $key => $need) {
            $quantityNeeded = $need->quantity_planned - $need->quantity_owned;


            $farmers = MasterFarmer::leftJoin("master_farmer_fertilizers", "master_farmers.id", "=", "master_farmer_fertilizers.id_master_farmer")
                ->leftJoin("master_fertilizers", "master_fertilizers.id", "=", "master_farmer_fertilizers.id_master_fertilizer")
                ->leftJoin("th_farmer_planned", "master_farmer_fertilizers.id_master_farmer", "=", "th_farmer_planned.id_master_farmer")
                ->leftJoin("td_farmer_planned", function ($join) {
                    $join->on("th_farmer_planned.id", "=", "td_farmer_planned.id_th_farmer_planned");
                    $join->on("td_farmer_planned.id_master_fertilizer", "=", "master_fertilizers.id");
                })
                ->leftJoin("td_fertilizer_distribution", "td_fertilizer_distribution.id_farmer_lender", "master_farmers.id")
                ->where("master_farmers.id", "<>", $need->farmer_id)
                ->where("master_farmer_fertilizers.id_master_fertilizer", $need->fertilizer_id)
                ->select(
                    "master_farmers.name as farmer_name",
                    "master_farmers.id as farmer_id",
                    "master_fertilizers.name as fertilizer_name",
                    "master_fertilizers.id as fertilizer_id",
                    "master_farmer_fertilizers.quantity_owned as quantity_owned",
                    "th_farmer_planned.code as planned_code",
                    DB::raw("COALESCE(SUM(td_farmer_planned.quantity_planned), 0) as total_quantity_planned"),
                    DB::raw("COALESCE(SUM(td_fertilizer_distribution.total_loan - td_fertilizer_distribution.total_return), 0) as total_lent"),
                    DB::raw("(master_farmer_fertilizers.quantity_owned - COALESCE(SUM(td_farmer_planned.quantity_planned), 0) - COALESCE(SUM(td_fertilizer_distribution.total_loan - td_fertilizer_distribution.total_return), 0)) as surplus"),
                    DB::raw("({$quantityNeeded}) as quantity_needed_to_lend")
                )
                ->groupBy(
                    "master_farmers.name",
                    "master_farmers.id",
                    "master_fertilizers.name",
                    "master_fertilizers.id",
                    "master_farmer_fertilizers.quantity_owned",
                    "th_farmer_planned.code",
                    "td_farmer_planned.id_master_fertilizer",
                    "th_farmer_planned.id_master_farmer"
                )
                ->havingRaw("quantity_owned - total_quantity_planned - total_lent > 0")
                ->orderBy("surplus", "DESC")
                ->get()
                ->toArray();


            $surplus = collect($farmers)->filter(function ($farmer) use ($farmers) {
                $totalCurrentLent = self::getTotalCurrentLent($farmers, $farmer['farmer_id'], $farmer['fertilizer_id']);
                return $farmer['quantity_owned'] -
                    $farmer['total_quantity_planned'] -
                    $farmer['total_lent'] -
                    $totalCurrentLent > 0;
            });

            if ($surplus->isEmpty()) {
                continue;
            }

            foreach ($surplus as $sur) {
                $val = $sur['surplus'] - self::getTotalCurrentLent($tracking_lent_arr, $sur['farmer_id'], $sur['fertilizer_id']);
                if ($val <= 0) {
                    continue;
                }
                $tracking_lent_arr[] = [
                    "id_farmer_lender" => $sur['farmer_id'],
                    "id_farmer_borrower" => $need->farmer_id,
                    "borrower_name" => $need->farmer_name,
                    "code" => $need->code,
                    "id_planning" => $need->id_planning,
                    "lender_name" => $sur['farmer_name'],
                    "id_fertilizer" => $sur['fertilizer_id'],
                    "fertilizer_name" => $sur['fertilizer_name'],
                    "borrower_quantity_owned" => number_format($need->quantity_owned, 2, ',', '.'),
                    "borrower_quantity_planned" => number_format($need->quantity_planned, 2, ',', '.'),
                    "borrower_quantity_needed" => number_format($quantityNeeded, 2, ',', '.'),
                    "surplus" => number_format($sur['surplus'], 2, ',', '.'),
                    "total_lent" => $val > $quantityNeeded ? $quantityNeeded : $val
                ];
            }
        }



        return response()->json($tracking_lent_arr);
    }



    public function listFarmerBorrower(Request $request)
    {
        $model = TDFertilizerDistribution::with(["THFarmerPlanned", "farmerLender", "MasterFertilizer"])
            ->where("id_farmer_borrower", $request->farmerBorrowerId)
            ->whereRaw("total_loan - total_return > 0");

        return DataTables::of($model)
            ->editColumn("code", function ($model) {
                return $model->THFarmerPlanned->code;
            })
            ->addColumn("lender_name", function ($model) {
                return $model->farmerLender->name;
            })
            ->addColumn("loan_remainder", function ($model) {
                return $model->total_loan - $model->total_return . " KG";
            })
            ->addColumn("fertilizer_name", function ($model) {
                return $model->MasterFertilizer->name;
            })
            ->addColumn("loan_date", function ($model) {
                return $model->created_at->format("d-m-Y");
            })
            ->addColumn("qty_return", function ($model) {
                return "<input type='number' class='form-control' name='qty_return' id='qty_return'  max='" . $model->total_loan - $model->total_return . "'  data-id='" . $model->id . "' value='0' min='0' />";
            })
            ->addColumn("action", function ($model) {
                return "<button type='button' class='btn btn-primary' id='returnLoanBtn' data-id='" . $model->id . "'>Kembalikan</button>";
            })
            ->rawColumns(["qty_return", "action"])
            ->toJson();
    }
}
