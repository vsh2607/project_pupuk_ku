<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterFertilizer;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MasterFertilizerController extends Controller
{
    public function index()
    {
        return view('master-fertilizer.index');
    }

    public function listData()
    {
        $model = MasterFertilizer::get(['name', 'id']);

        return DataTables::of($model)
            ->addColumn('action', function ($model) {
                $infoButton = "&nbsp;<a href='" . url('master-data/master-fertilizer/' . $model->id . '/info') . "' class='btn  btn-primary d-inline-block'><i class='fas fa-info'></i></a>";
                $editButton = "&nbsp;<a href='" . url('master-data/master-fertilizer/' . $model->id . '/edit') . "' class='btn  btn-warning d-inline-block'><i class='fas fa-edit'></i></a>";
                $deleteButton = "&nbsp;<a href='#' class='btn  btn-danger d-inline-block'><i class='fas fa-trash'></i></a>";

                return $infoButton . $editButton;
            })
            ->toJson();
    }

    public function addForm()
    {
        return view('master-fertilizer.add');
    }

    public function addData(Request $request)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->only('name');
            MasterFertilizer::create($requestData);
            DB::commit();

            return redirect('/master-data/master-fertilizer')->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/master-data/master-fertilizer')->with('error', $e->getMessage());
        }
    }

    public function editForm($id)
    {
        $data = MasterFertilizer::findOrFail($id);
        return view('master-fertilizer.edit', ['data' => $data]);
    }

    public function updateData(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->only('name');
            MasterFertilizer::where('id', $id)->update($requestData);
            DB::commit();

            return redirect('/master-data/master-fertilizer')->with('success', 'Data berhasil diubah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/master-data/master-fertilizer')->with('error', $e->getMessage());
        }
    }

    public function viewForm($id)
    {
        $data = MasterFertilizer::findOrFail($id);
        return view('master-fertilizer.info', ['data' => $data]);
    }

    public function listAllDataFertilizer(Request $request)
    {
        $data = $request->all();
        $search_word = !empty($data) ? $data["name"] : '';
        $data = MasterFertilizer::where('name', 'LIKE', '%' . $search_word . '%');
        $data = $data->get(['id', 'name']);
        return response()->json($data);
    }
}
