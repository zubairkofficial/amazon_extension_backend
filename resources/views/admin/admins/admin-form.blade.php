@extends('admin.layout')

@section('content')
<h1 class="mb-2">{{ $formType === 'add' ? 'Create' : 'Update' }} Admin</h1>

<div class="text-end">
    <a href="/admin/admins" class="btn btn-primary">Show All</a>
</div>

<form method="POST" action="{{ $formType === 'update' ? " /admin/admins/{$user->id}" : '/admin/admins' }}">
    @csrf
    @if($formType === 'update')
    @method('PUT')
    @endif
    <div class="form-group mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $user->name ?? '' }}"
            autocomplete="name">
        @error('name')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="text" class="form-control" id="email" name="email" value="{{ old('email') ?? $user->email ?? '' }}"
            autocomplete="email">
        @error('email')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="form-group mb-3">
        <label for="password" class="form-label">{{ $formType === 'update' ? 'Change ' : '' }}Password</label>
        @if($formType === 'update')
        <i class="fs-6">(Leave Empty if you don't want to change password.)</i>
        @endif
        <div class="input-group">
            <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
            <button onclick=" password.type = password.type === 'text' ? 'password' : 'text' " type="button"
                class="btn btn-light">👁</button>
        </div>
        @error('password')
        <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="text-end">
        <button class="btn btn-primary">{{ $formType === 'add' ? 'Add' : 'Update' }}</button>
    </div>
</form>
@endsection