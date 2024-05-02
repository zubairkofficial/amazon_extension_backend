<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.admins', [
            'users' => User::where('type', 'admin')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.admin-form', [
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
            'email' => 'email|required|unique:users,email',
            'password' => 'required',
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['type'] = 'admin';
        User::create($data);
        return response()->redirectTo('/admin/admins')->with('success', 'Admin created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $admin)
    {
        return view('admin.admin', [
            'user' => $admin
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $admin)
    {
        return view('admin.admin-form', [
            'user' => $admin,
            'formType' => 'update'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $admin)
    {   
        $data = $request->validate([
            'name' => 'required',
            'email' => 'email|required|unique:users,email,'.$admin->id,
            'password' => 'nullable',
        ]);
        
        if ($data['password'])
            $data['password'] = Hash::make($data['password']);
        else
            unset($data['password']);

        $admin->update($data);
        return response()->redirectTo('/admin/admins')->with('success', 'Admin updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin)
    {
        $admin->delete();
        return response()->redirectTo('/admin/admins')->with('success', 'Admin deleted successfully.');
    }

    public function admin_profile()
    {
        return view('admin.profile');
    }
}
