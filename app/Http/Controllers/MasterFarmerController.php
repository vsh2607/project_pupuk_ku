<?php

namespace App\Http\Controllers;

use App\Models\MasterFarmer;
use App\Models\MasterFarmerPlant;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class MasterFarmerController extends Controller
{
    public function index()
    {
        $data = MasterFarmer::all();
        return view('master-farmer.index', ['data' => $data]);
    }

    public function listData(Request $request)
    {
        $model = MasterFarmer::orderBy('id', 'DESC');

        return DataTables::of($model)
            ->addColumn('action', function ($model) {
                $infoButton = "&nbsp;<a href='" . url('master-data/master-farmer/' . $model->id . '/info') . "' class='btn  btn-primary d-inline-block'><i class='fas fa-info'></i></a>";
                $editButton = "&nbsp;<a href='" . url('master-data/master-farmer/' . $model->id . '/edit') . "' class='btn  btn-warning d-inline-block'><i class='fas fa-edit'></i></a>";
                $deleteButton = "&nbsp;<a href='#' class='btn  btn-danger d-inline-block'><i class='fas fa-trash'></i></a>";
                $mapButton = '<button class="btn btn-success d-inline-block btn-map" type="button" data-location="' . $model->land_location . '"><i class="fas fa-map-marked-alt"></i></button>';
                return  $mapButton . $infoButton . $editButton;
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
            ->rawColumns(['action', 'land_area', 'land_type'])
            ->toJson();
    }

    public function addForm()
    {
        return view('master-farmer.add');
    }

    public function addData(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'land_type' => 'required',
            'handphone_number' => 'required',
            'land_area' => 'required',
            'land_location' => 'required',
            'fertilizer_quantity_owned' => 'required',
            'fertilizer_quantity_needed' => 'required',
            'plant_type' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $masterFarmer = MasterFarmer::updateOrCreate(
                [
                    'name' => $request->name,
                    'land_type' => $request->land_type,
                    'handphone_number' => $request->handphone_number,
                    'land_area' => $request->land_area,
                    'land_location' => $request->land_location,
                    'fertilizer_quantity_owned' => $request->fertilizer_quantity_owned,
                    'fertilizer_quantity_needed' => $request->fertilizer_quantity_needed,
                ]
            );
            foreach ($request->plant_type as  $plant_type) {
                MasterFarmerPlant::updateOrCreate(
                    [
                        'id_master_farmer' => $masterFarmer->id,
                        'id_master_plant' => $plant_type
                    ]
                );
            }

            DB::commit();
            return redirect('master-data/master-farmer')->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('master-data/master-farmer')->with('error', $e->getMessage());
        }
    }

    public function editForm($id)
    {
        $data = MasterFarmer::find($id);
        $plants = MasterFarmerPlant::with(['plant'])->where('id_master_farmer', $id)->get();
        $selectedPlants = $plants->pluck('plant.id')->toArray();
        return view('master-farmer.edit', ['data' => $data, 'plants' => $plants, 'selectedPlants' => $selectedPlants]);
    }

    public function updateData(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'land_type' => 'required',
            'handphone_number' => 'required',
            'land_area' => 'required',
            'land_location' => 'required',
            'fertilizer_quantity_owned' => 'required',
            'fertilizer_quantity_needed' => 'required',
            'plant_type' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $data = MasterFarmer::find($id);
            MasterFarmerPlant::where('id_master_farmer', $id)->delete();
            if ($data) {
                $data->update([
                    'name' => $request->name,
                    'land_type' => $request->land_type,
                    'handphone_number' => $request->handphone_number,
                    'land_area' => $request->land_area,
                    'land_location' => $request->land_location,
                    'fertilizer_quantity_owned' => $request->fertilizer_quantity_owned,
                    'fertilizer_quantity_needed' => $request->fertilizer_quantity_needed,
                ]);


                foreach ($request->plant_type as  $plant_type) {
                    MasterFarmerPlant::updateOrCreate(
                        [
                            'id_master_farmer' => $data->id,
                            'id_master_plant' => $plant_type
                        ]
                    );
                }
            }
            DB::commit();
            return redirect('master-data/master-farmer')->with('success', 'Data berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('master-data/master-farmer')->with('error', $e->getMessage());
        }
    }

    public function viewForm($id)
    {
        $data = MasterFarmer::find($id);
        $plants = MasterFarmerPlant::with(['plant'])->where('id_master_farmer', $id)->get();
        $selectedPlants = $plants->pluck('plant.id')->toArray();
        return view('master-farmer.info', ['data' => $data, 'plants' => $plants, 'selectedPlants' => $selectedPlants]);
    }


    public function listLenderCandidates(Request $request)
    {

        $data = $request->all();
        $search_word = !empty($data) ? $data["name"] : '';
        $data = MasterFarmer::whereRaw('fertilizer_quantity_owned - fertilizer_quantity_needed > 0')->where('name', 'LIKE', '%' . $search_word . '%')
            ->select('id', DB::raw("CONCAT(name, ' (', fertilizer_quantity_owned - fertilizer_quantity_needed, ' KG)') as name"), DB::raw('fertilizer_quantity_owned - fertilizer_quantity_needed as max'))
            ->get();

        return response()->json($data);
    }


    public function listBorrowerCandidates(Request $request)
    {
        $data = $request->all();
        $search_word = !empty($data) ? $data["name"] : '';
        $data = MasterFarmer::whereRaw('fertilizer_quantity_owned - fertilizer_quantity_needed < 0')->where('name', 'LIKE', '%' . $search_word . '%')
            ->select('id', DB::raw("CONCAT(name, ' (', fertilizer_quantity_owned - fertilizer_quantity_needed, ' KG)') as name"), DB::raw('fertilizer_quantity_owned - fertilizer_quantity_needed as max'))
            ->get();

        return response()->json($data);
    }
}
