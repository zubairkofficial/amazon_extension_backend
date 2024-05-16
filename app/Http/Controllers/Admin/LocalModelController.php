<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LocalModel;

class LocalModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.localModel.models', [
            'models' => LocalModel::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.localModel.model-form', [
            'formType' => 'add'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'type' => 'required',
            'baseUrl' => 'required',
            'max_tokens' => 'nullable',
            'top_p' => 'nullable',
            'temp' => 'nullable',
            'seed' => 'nullable',
            'mode' => 'nullable',
            'instruction_template' => 'nullable',
            'character' => 'nullable',
        ]);
        LocalModel::create($data);
        return response()->redirectTo('/admin/localmodels')->with('success', 'Local Model created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LocalModel $localmodel)
    {
        return view('admin.localModel.model', [
            'model' => $localmodel
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LocalModel $localmodel)
    {   
        return view('admin.localModel.model-form', [
            'model' => $localmodel,
            'formType' => 'update'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LocalModel $localmodel)
    {
        $data = $request->validate([
            'name' => 'required',
            'type' => 'required',
            'baseUrl' => 'required',
            'max_tokens' => 'nullable',
            'top_p' => 'nullable',
            'temp' => 'nullable',
            'seed' => 'nullable',
            'mode' => 'nullable',
            'instruction_template' => 'nullable',
            'character' => 'nullable',
        ]);

        $localmodel->update($data);
        return response()->redirectTo('/admin/localmodels')->with('success', 'Local Model updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LocalModel $localmodel)
    {
        $localmodel->delete();
        return response()->redirectTo('/admin/localmodels')->with('success', 'Local Model deleted successfully.');
    }
}
