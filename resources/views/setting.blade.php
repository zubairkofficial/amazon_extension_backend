<x-admin-layout>

    <div class="container overflow-hidden">

        <div class="p-3 row gx-4 gy-4">

            <form method="post" class="col-lg-6" action="{{ route('profile.update') }}">

                <div class="rounded shadow-sm p-4">

                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Profile Information') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Update your account's profile information and email address.") }}
                    </p>

                    @csrf
                    @method('patch')

                    <div class="form-group">
                        <label class="form-label">Name</label>
                        <div class="form-control-wrap">
                            <input class="form-control" id="name" name="name" type="text" placeholder="Enter name"
                                value="{{old('name', Auth::user()->name) }}" required autofocus autocomplete="name" />
                            <small class="text-danger"></small>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">Email</label>
                        <div class="form-control-wrap">
                            <input class="form-control" type="text" placeholder="Enter Email " id="email" name="email"
                                type="email" class="mt-1 block w-full" value="{{old('email', Auth::user()->email)}}"
                                required autocomplete="username" />
                            <small class="text-danger"></small>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>

                </div>

            </form>


            <form method="post" class="col-lg-6" action="{{ route('password.update') }}">

                <div class="rounded shadow-sm p-4">

                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Update Password') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Ensure your account is using a long, random password to stay secure.") }}
                    </p>

                    @csrf
                    @method('put')

                    <div class="form-group">
                        <label class="form-label" for="update_password_current_password">Current
                            Password</label>
                        <div class="form-control-wrap">
                            <input class="form-control" id="update_password_current_password" name="current_password"
                                type="password" autocomplete="current-password" />
                            <small class="text-danger"></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="update_password_password">New
                            Password</label>
                        <div class="form-control-wrap">
                            <input class="form-control" id="update_password_password" name="password" type="password"
                                autocomplete="new-password" />
                            <small class="text-danger"></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="update_password_password_confirmation">Confirm
                            Password</label>
                        <div class="form-control-wrap">
                            <input class="form-control" id="update_password_password_confirmation"
                                name="password_confirmation" type="password" autocomplete="new-password" />
                            <small class="text-danger"></small>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>

                </div>

            </form>

        </div>
    </div>

</x-admin-layout>
