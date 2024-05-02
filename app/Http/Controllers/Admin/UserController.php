<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.users', [
            'users' => User::where('type', 'user')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user-form', [
            'formType' => 'add'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|numeric|unique:users,id',
            'name' => 'required',
            'email' => 'email|required|unique:users,email',
            'password' => 'required',
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return response()->redirectTo('/admin/users')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.user', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.user-form', [
            'user' => $user,
            'formType' => 'update'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'email|required',
            'password' => '',
        ]);

        if ($data['password'])
            $data['password'] = Hash::make($data['password']);
        else
            unset($data['password']);

        $user->update($data);
        return response()->redirectTo('/admin/users')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->redirectTo('/admin/users')->with('success', 'User deleted successfully.');
    }

    public function admin_profile()
    {
        return view('admin.profile');
    }
}
