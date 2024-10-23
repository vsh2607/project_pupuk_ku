<?php

namespace App\Http\Controllers;

use App\Models\TDFarmerPlanned;
use Illuminate\Http\Request;
use App\Models\THFarmerPlanned;
use Illuminate\Support\Facades\DB;
use App\Models\TDFarmerPlantPlanned;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Yajra\DataTables\Facades\DataTables;

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
            ->editColumn('land_area', function($model){
                return $model->land_area . ' m<sup>2</sup>';
            })
            ->addColumn('fertilizer_needs', function ($model) {
                $fertilizerNeeds = '';
                $fertilizerNeeds = '<ul>';
                foreach ($model->TDFarmerPlanned as $fertilizer) {
                    $fertilizerNeeds .= '<li>' . $fertilizer->MasterFertilizer->name . ' : ' . $fertilizer->quantity_planned . ' KG'  .'</li>';
                }
                $fertilizerNeeds .= '</ul>';
                return $fertilizerNeeds;
            })
            ->addColumn('action', function($model){
                $editButton = "<a href='" . url('module-management/planting-plan/' . $model->id . '/edit') . "' class='btn btn-warning'><i class='fas fa-edit'></i></a>";
                $deleteButton = "&nbsp;<button class='btn btn-danger btn-delete' data-toggle='modal' data-target='#deleteModal' data-id='" . $model->id . "'><i class='fas fa-trash'></i></button>";
                // $deleteButton = "&nbsp;<a href='" . url('module-management/planting-plan/' . $model->id . '/delete') . "' class='btn btn-danger'><i class='fas fa-trash'></i></a>";
                return $editButton . $deleteButton;
            })
            ->rawColumns(['planned_plant', 'fertilizer_needs', 'land_area', 'action'])
            ->toJson();
    }

    public function addForm()
    {
        return view('module-planting-plan.add');
    }

    public function editForm($id){
        $thFarmerPlanned = THFarmerPlanned::with(['MasterFarmer', 'TDFarmerPlantPlanned.MasterPlant', 'TDFarmerPlanned.MasterFertilizer'])->find($id);
        $thFarmerPlannedPlant = $thFarmerPlanned->TDFarmerPlantPlanned->pluck('id_master_plant')->toArray();
        return view('module-planting-plan.edit', ['data' => $thFarmerPlanned, 'data_plant' => $thFarmerPlannedPlant]);
    }

    public function updateData($id, Request $request){
        DB::beginTransaction();
        try{
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
        }catch(\Exception $e){
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

    public function deleteData(Request $request){
        DB::beginTransaction();
        try{
            THFarmerPlanned::find($request->id_th_farmer_planned)->delete();
            TDFarmerPlanned::where('id_th_farmer_planned', $request->id_th_farmer_planned)->delete();
            TDFarmerPlantPlanned::where('id_th_farmer_planned', $request->id_th_farmer_planned)->delete();

            DB::commit();
            return redirect('module-management/planting-plan')->with('success', 'Data berhasil dihapus');
        }catch(\Exception $e){
            DB::rollback();
            return redirect('module-manademnet/planting-plan')->with('error', $e->getMessage());
        }
    }


}
