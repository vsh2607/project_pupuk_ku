<?php

namespace App\Http\Controllers;

use App\Models\MasterPlant;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MasterPlantController extends Controller
{
    public function index()
    {
        return view('master-plant.index');
    }

    public function listData(Request $request)
    {
        $model = MasterPlant::orderBy('id', 'DESC');
        return DataTables::of($model)
            ->addColumn('action', function ($model) {
                $infoButton = "<a href='" . url('master-data/master-plant/' . $model->id . '/info') . "' class='btn  btn-primary d-inline-block'><i class='fas fa-info'></i></a>";
                $editButton = "&nbsp;<a href='" . url('master-data/master-plant/' . $model->id . '/edit') . "' class='btn  btn-warning d-inline-block'><i class='fas fa-edit'></i></a>";
                $deleteButton = "&nbsp;<a href='#' class='btn  btn-danger d-inline-block'><i class='fas fa-trash'></i></a>";
                return $infoButton . $editButton;
            })
            ->toJson();
    }

    public function addForm()
    {
        return view('master-plant.add');
    }

    public function addData(Request $request)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->only('name');
            MasterPlant::create($requestData);
            DB::commit();

            return redirect('/master-data/master-plant')->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/master-data/master-plant')->with('error', $e->getMessage());
        }
    }


    public function editForm($id)
    {
        $data = MasterPlant::findOrFail($id);
        return view('master-plant.edit', ['data' => $data]);
    }

    public function updateData(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->only('name');
            $data = MasterPlant::find($id);
            if ($data) {
                $data->update($requestData);
            }

            DB::commit();

            return redirect('/master-data/master-plant')->with('success', 'Data berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/master-data/master-plant')->with('error', $e->getMessage());
        }
    }

    public function viewForm($id)
    {
        $data = MasterPlant::find($id);
        return view('master-plant.info', ['data' => $data]);
    }


    public function listAllDataPlant (Request $request){

        $data = $request->all();
        $search_word = !empty($data) ? $data["name"] : '';
        $data = MasterPlant::where('name', 'LIKE', '%' . $search_word . '%');
        $data = $data->get(['id', 'name']);
        return response()->json($data);
    }
}
