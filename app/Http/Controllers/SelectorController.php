<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Selector;

class SelectorController extends Controller
{
    public function index()
    {
        $selectors = Selector::all();
        return view('selectors.index', compact('selectors'));
    }

    public function create()
    {
        return view('selectors.create');
    }

    public function store(Request $request)
    {
        $seletor = new Selector();
        $seletor->name = $request->name;
        $seletor->selector = $request->selector;
        $seletor->type = $request->type;
        $seletor->status = $request->status;
        $seletor->save();
        return redirect()->route('selectors.index')->with('success', 'Selector created successfully.');
    }
    public function edit($id)
    {
        $selector = Selector::find($id);
        return view('selectors.edit', compact('selector'));
    }
    public function update(Request $request, $id)
    {
        $seletor = Selector::find($id);
        $seletor->name = $request->name;
        $seletor->selector = $request->selector;
        $seletor->type = $request->type;
        $seletor->status = $request->status;
        $seletor->save();
        return redirect()->route('selectors.index')->with('success', 'Selector updated successfully.');
    }
    public function destroy($id)
    {
        Selector::find($id)->delete();
        return redirect()->route('selectors.index')->with('success', 'Selector deleted successfully.');
    }

    public function getall()
    {
        return Selector::where('status', 'enable')->get();
    }
}
