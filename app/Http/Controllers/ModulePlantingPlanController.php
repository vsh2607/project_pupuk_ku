<?php

namespace App\Http\Controllers;

use App\Models\TDFarmerPlanned;
use Illuminate\Http\Request;
use App\Models\THFarmerPlanned;
use Illuminate\Support\Facades\DB;
use App\Models\TDFarmerPlantPlanned;
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
                $editButton = "&nbsp;<a href='" . url('module-management/planting-plan/' . $model->id . '/edit') . "' class='btn btn-warning'><i class='fas fa-edit'></i></a>";
                $deleteButton = "&nbsp;<a href='" . url('module-management/planting-plan/' . $model->id . '/delete') . "' class='btn btn-danger'><i class='fas fa-trash'></i></a>";
                return $editButton . $deleteButton;
            })
            ->rawColumns(['planned_plant', 'fertilizer_needs', 'land_area', 'action'])
            ->toJson();
    }

    public function addForm()
    {
        return view('module-planting-plan.add');
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
                    'quantity_planned' => $request->fertilizer_qty_planned[$key]
                ]);
            }

            DB::commit();
            return redirect('module-management/planting-plan')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('module-management/planting-plan')->with('error', $e->getMessage());
        }
    }
}
