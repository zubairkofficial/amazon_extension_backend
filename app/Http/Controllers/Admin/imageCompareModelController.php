<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImageCompareModel;
use App\Models\ScrapeProduct;
use App\Models\SystemProduct;
use Illuminate\Support\Facades\Schema;

class imageCompareModelController extends Controller
{
    /**
    * Display a listing of the resource.
    */
   public function index()
   {
       return view('admin.imgCompModel.models', [
           'models' => ImageCompareModel::all(),
       ]);
   }

   /**
    * Show the form for creating a new resource.
    */
   public function create()
   {
       return view('admin.imgCompModel.model-form', [
           'formType' => 'add',
           'models' => ImageCompareModel::all(),
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
           'imageCompare_prompt' => 'required',
           'json' => 'required'
       ]);
       ImageCompareModel::create($data);
       return response()->redirectTo('/admin/imgCompModels')->with('success', 'Image Compare Model created successfully.');
   }

   /**
    * Display the specified resource.
    */
   public function show(ImageCompareModel $imgCompModel)
   {
       return view('admin.imgCompModel.model', [
           'model' => $imgCompModel,
       ]);
   }

   /**
    * Show the form for editing the specified resource.
    */
   public function edit(ImageCompareModel $imgCompModel)
   {   
       return view('admin.imgCompModel.model-form', [
           'model' => $imgCompModel,
           'formType' => 'update',
           'models' => ImageCompareModel::all(),
           'scrapeArguments' => Schema::getColumnListing((new ScrapeProduct)->getTable()),
           'systemArguments' => Schema::getColumnListing((new SystemProduct)->getTable()),
       ]);
   }

   /**
    * Update the specified resource in storage.
    */
   public function update(Request $request, ImageCompareModel $imgCompModel)
   {
       $data = $request->validate([
           'name' => 'required',
           'value' => 'required',
           'temp' => 'required',
           'imageCompare_prompt' => 'required',
           'json' => 'required'
       ]);

       $imgCompModel->update($data);
       return response()->redirectTo('/admin/imgCompModels')->with('success', 'Image Compare Model updated successfully.');
   }

   /**
    * Remove the specified resource from storage.
    */
   public function destroy(ImageCompareModel $imgCompModel)
   {
       $imgCompModel->delete();
       return response()->redirectTo('/admin/imgCompModels')->with('success', 'Image Compare Model deleted successfully.');
   }
}
