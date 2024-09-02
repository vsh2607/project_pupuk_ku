<?php

namespace App\Http\Controllers;

use App\Models\MasterFarmer;
use Illuminate\Http\Request;
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
        $model = MasterFarmer::with(['farmerPlants', 'farmerPlants.plant']);

        return DataTables::of($model)
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
                $fertilizer_quantity_remainder = $model->fertilizer_quantity_owned - $model->fertilizer_quantity_needed;
                return $fertilizer_quantity_remainder . ' KG';
            })
            ->addColumn('status', function ($model) {
                $fertilizer_quantity_remainder = $model->fertilizer_quantity_owned - $model->fertilizer_quantity_needed;
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
            ->rawColumns(['action', 'land_area', 'land_type', 'plant_type', 'status'])
            ->toJson();
    }


    public function addForm(){
        return view('module-fertilizer-distribution.add');
    }
}
