<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TDFarmerPlanned;
use App\Models\THFarmerPlanned;
use Illuminate\Support\Facades\DB;
use App\Models\TDFarmerPlantPlanned;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ModulePlantingPlanController extends Controller
{
    public function index()
    {
        return view('module-planting-plan.index');
    }

    public function listData()
    {
        $model = THFarmerPlanned::with(['MasterFarmer', 'TDFarmerPlantPlanned.MasterPlant', 'TDFarmerPlanned.MasterFertilizer'])->orderBy('id', 'DESC');
        return DataTables::of($model)
            ->addColumn('farmer_name', function ($model) {
                return $model->MasterFarmer->name;
            })
            ->addColumn('planned_plant', function ($model) {
                $plantTypes = '';
                $plantTypes = '<ul>';
                foreach ($model->TDFarmerPlantPlanned as $plantType) {
                    $plantTypes .= '<li>' . $plantType->MasterPlant->name . '</li>';
                }
                $plantTypes .= '</ul>';
                return $plantTypes;
            })
            ->editColumn('land_area', function ($model) {
                return $model->land_area . ' m<sup>2</sup>';
            })
            ->addColumn('fertilizer_needs', function ($model) {
                $fertilizerNeeds = '';
                $fertilizerNeeds = '<ul>';
                foreach ($model->TDFarmerPlanned as $fertilizer) {
                    $fertilizerNeeds .= '<li>' . $fertilizer->MasterFertilizer->name . ' : ' . $fertilizer->quantity_planned . ' KG'  . '</li>';
                }
                $fertilizerNeeds .= '</ul>';
                return $fertilizerNeeds;
            })
            ->addColumn('action', function ($model) {
                $editButton = "<a href='" . url('module-management/planting-plan/' . $model->id . '/edit') . "' class='btn btn-warning'><i class='fas fa-edit'></i></a>";
                $deleteButton = "&nbsp;<button class='btn btn-danger btn-delete' data-toggle='modal' data-target='#deleteModal' data-id='" . $model->id . "'><i class='fas fa-trash'></i></button>";
                return $editButton;
            })
            ->rawColumns(['planned_plant', 'fertilizer_needs', 'land_area', 'action'])
            ->toJson();
    }

    public function addForm()
    {
        return view('module-planting-plan.add');
    }

    public function editForm($id)
    {
        $thFarmerPlanned = THFarmerPlanned::with(['MasterFarmer', 'TDFarmerPlantPlanned.MasterPlant', 'TDFarmerPlanned.MasterFertilizer'])->find($id);
        $thFarmerPlannedPlant = $thFarmerPlanned->TDFarmerPlantPlanned->pluck('id_master_plant')->toArray();
        return view('module-planting-plan.edit', ['data' => $thFarmerPlanned, 'data_plant' => $thFarmerPlannedPlant]);
    }

    public function updateData($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $THFarmerPlanned = THFarmerPlanned::find($id);
            TDFarmerPlanned::where('id_th_farmer_planned', $id)->delete();
            TDFarmerPlantPlanned::where('id_th_farmer_planned', $id)->delete();

            $THFarmerPlanned->update([
                'id_master_farmer' => $request->id_master_farmer,
                'planned_date' => $request->date,
                'land_area' => $request->land_area,
                'status' => 0
            ]);

            foreach ($request->plant_type as $key => $value) {
                TDFarmerPlantPlanned::create([
                    'id_th_farmer_planned' => $THFarmerPlanned->id,
                    'id_master_plant' => $value
                ]);
            }

            foreach ($request->fertilizer_name as $key => $value) {
                TDFarmerPlanned::create([
                    'id_th_farmer_planned' => $THFarmerPlanned->id,
                    'id_master_fertilizer' => $value,
                    'quantity_planned' => $request->fertilizer_qty_planned[$key],
                    'quantity_owned' => $request->fertilizer_qty_owned[$key]
                ]);
            }

            DB::commit();
            return redirect("module-management/planting-plan")->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('module-management/planting-plan')->with('error', $e->getMessage());
        }
    }


    public function addData(Request $request)
    {

        DB::beginTransaction();
        try {
            $THFarmerPlanned = THFarmerPlanned::create([
                'id_master_farmer' => $request->id_master_farmer,
                'planned_date' => $request->date,
                'land_area' => $request->land_area,
                'code' => 'PP' . date('YmdHis'),
                'status' => 0
            ]);

            foreach ($request->plant_type as $key => $value) {
                TDFarmerPlantPlanned::create([
                    'id_th_farmer_planned' => $THFarmerPlanned->id,
                    'id_master_plant' => $value
                ]);
            }

            foreach ($request->fertilizer_name as $key => $value) {
                TDFarmerPlanned::create([
                    'id_th_farmer_planned' => $THFarmerPlanned->id,
                    'id_master_fertilizer' => $value,
                    'quantity_planned' => $request->fertilizer_qty_planned[$key],
                    'quantity_owned' => $request->fertilizer_qty_owned[$key]
                ]);
            }

            DB::commit();
            return redirect('module-management/planting-plan')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('module-management/planting-plan')->with('error', $e->getMessage());
        }
    }

    public function deleteData(Request $request)
    {
        DB::beginTransaction();
        try {
            THFarmerPlanned::find($request->id_th_farmer_planned)->delete();
            TDFarmerPlanned::where('id_th_farmer_planned', $request->id_th_farmer_planned)->delete();
            TDFarmerPlantPlanned::where('id_th_farmer_planned', $request->id_th_farmer_planned)->delete();

            DB::commit();
            return redirect('module-management/planting-plan')->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('module-manademnet/planting-plan')->with('error', $e->getMessage());
        }
    }

    public function listPlantingPlan(Request $request)
    {
        $farmer_borrower_id = $request->farmerBorrowerId;
        $data = THFarmerPlanned::with(["TDFarmerPlanned.MasterFertilizer", "TDFarmerPlantPlanned"])
            ->where("id_master_farmer", $farmer_borrower_id);




        return DataTables::of($data)
            ->editColumn("land_area", function ($data) {
                return $data->land_area . " m<sup>2</sup>";
            })
            // ->addColumn("fertilizers_owned", function($data){
            //     $fertilizers = "";
            //     $fertilizers = "<ul>";
            //     foreach($data->MasterFarmer->MasterFarmerFertilizer as $fertilizer){
            //         $fertilizers .= "<li style='white-space: nowrap;'>" . $fertilizer->MasterFertilizer->name . " : " . $fertilizer->quantity_owned . " KG</li>";
            //     }
            //     $fertilizers .= "</ul>";
            //     return $fertilizers;
            // })
            ->addColumn("fertilizers_owned", function ($data) {
                $fertilizers = "";
                $fertilizers = "<ul>";
                foreach ($data->TDFarmerPlanned as $fertilizer) {
                    $fertilizers .= "<li style='white-space: nowrap;'>" . $fertilizer->MasterFertilizer->name . " : " . $fertilizer->quantity_owned . " KG</li>";
                }
                $fertilizers .= "</ul>";
                return $fertilizers;
            })
            ->addColumn("fertilizers_planned", function ($data) {
                $fertilizers = "";
                $fertilizers = "<ul>";
                foreach ($data->TDFarmerPlanned as $fertilizer) {
                    $fertilizers .= "<li style='white-space: nowrap;'>" . $fertilizer->MasterFertilizer->name . " : " . $fertilizer->quantity_planned . " KG</li>";
                }
                $fertilizers .= "</ul>";
                return $fertilizers;
            })
            ->addColumn("info", function ($data) {

                $info = "";
                $info = "<ul>";
                foreach ($data->TDFarmerPlanned as $fertilizer) {

                    if ($fertilizer->quantity_owned < $fertilizer->quantity_planned) {
                        $info .= "<li style='white-space: nowrap;' class='text-danger'>" . $fertilizer->MasterFertilizer->name . " : Kurang " . ($fertilizer->quantity_planned - $fertilizer->quantity_owned) . " KG</li>";
                    } else if ($fertilizer->quantity_owned > $fertilizer->quantity_planned) {
                        $info .= "<li style='white-space: nowrap;' class='text-success'>" . $fertilizer->MasterFertilizer->name . " : Lebih " . ($fertilizer->quantity_owned - $fertilizer->quantity_planned) . " KG</li>";
                    }else{
                        $info .= "<li style='white-space: nowrap;' class='text-success'>" . $fertilizer->MasterFertilizer->name . " : Cukup</li>";
                    }

                }
                $info .= "</ul>";
                return $info;
            })

            ->rawColumns(["land_area", "fertilizers_owned", "fertilizers_planned", "info"])
            ->toJson();
    }
}
