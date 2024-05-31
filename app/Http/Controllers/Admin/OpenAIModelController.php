<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OpenAIModel;
use App\Models\ScrapeProduct;
use App\Models\SystemProduct;
use Illuminate\Support\Facades\Schema;

class OpenAIModelController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.openaiModel.models', [
            'models' => OpenAIModel::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.openaiModel.model-form', [
            'formType' => 'add',
            'models' => OpenAIModel::all(),
            'scrapeArguments' => Schema::getColumnListing((new ScrapeProduct)->getTable()),
            'systemArguments' => Schema::getColumnListing((new SystemProduct)->getTable()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'value' => 'required',
            'temp' => 'required',
            'openai_prompt' => 'required',
            'json' => 'required'
        ]);
        OpenAIModel::create($data);
        return response()->redirectTo('/admin/openaimodels')->with('success', 'OpenAI Model created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OpenAIModel $openaimodel)
    {
        return view('admin.openaiModel.model', [
            'model' => $openaimodel,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OpenAIModel $openaimodel)
    {   
        return view('admin.openaiModel.model-form', [
            'model' => $openaimodel,
            'formType' => 'update',
            'models' => OpenAIModel::all(),
            'scrapeArguments' => Schema::getColumnListing((new ScrapeProduct)->getTable()),
            'systemArguments' => Schema::getColumnListing((new SystemProduct)->getTable()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OpenAIModel $openaimodel)
    {
        $data = $request->validate([
            'name' => 'required',
            'value' => 'required',
            'temp' => 'required',
            'openai_prompt' => 'required',
            'json' => 'required'
        ]);

        $openaimodel->update($data);
        return response()->redirectTo('/admin/openaimodels')->with('success', 'OpenAI Model updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OpenAIModel $openaimodel)
    {
        $openaimodel->delete();
        return response()->redirectTo('/admin/openaimodels')->with('success', 'OpenAI Model deleted successfully.');
    }
}
