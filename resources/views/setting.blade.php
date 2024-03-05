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

    {{-- <div class="py-12 px-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <div class="nk-block">
                        <div class="card shadown-none">
                            <div class="card-body">
                                <header>
                                    <h2 class="text-lg font-medium text-gray-900">
                                        {{ __('Profile Information') }}
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ __("Update your account's profile information and email address.") }}
                                    </p>
                                </header>
                                <div class="row g-3 gx-gs">
                                    <form method="post" action="{{ route('profile.update') }}">
                                        @csrf
                                        @method('patch')
                                        <div class="col-md-12">
                                            <div class="form-group ">
                                                <div class="row col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Name</label>
                                                        <div class="form-control-wrap">
                                                            <input class="form-control" id="name" name="name"
                                                                type="text" placeholder="Enter name"
                                                                value="{{old('name', Auth::user()->name) }}" required
                                                                autofocus autocomplete="name" />
                                                            <small class="text-danger"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Email</label>
                                                        <div class="form-control-wrap">
                                                            <input class="form-control" type="text"
                                                                placeholder="Enter Email " id="email" name="email"
                                                                type="email" class="mt-1 block w-full"
                                                                value="{{old('email', Auth::user()->email)}}" required
                                                                autocomplete="username" />
                                                            <small class="text-danger"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-1 m-1">
                                                    <button type="submit" class="btn btn-primary">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <div class="nk-block">
                        <div class="card shadown-none">
                            <div class="card-body">
                                <header>
                                    <h2 class="text-lg font-medium text-gray-900">
                                        {{ __('Update Password') }}
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ __('Ensure your account is using a long, random password to stay secure.') }}
                                    </p>
                                </header>
                                <div class="row g-3 gx-gs">
                                    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                                        @csrf
                                        @method('put')
                                        <div class="col-md-12">
                                            <div class="form-group ">
                                                <div class="row col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="update_password_current_password">Current
                                                            Password</label>
                                                        <div class="form-control-wrap">
                                                            <input class="form-control"
                                                                id="update_password_current_password"
                                                                name="current_password" type="password"
                                                                autocomplete="current-password" />
                                                            <small class="text-danger"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label" for="update_password_password">New
                                                            Password</label>
                                                        <div class="form-control-wrap">
                                                            <input class="form-control" id="update_password_password"
                                                                name="password" type="password"
                                                                autocomplete="new-password" />
                                                            <small class="text-danger"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="update_password_password_confirmation">Confirm
                                                            Password</label>
                                                        <div class="form-control-wrap">
                                                            <input class="form-control"
                                                                id="update_password_password_confirmation"
                                                                name="password_confirmation" type="password"
                                                                autocomplete="new-password" />
                                                            <small class="text-danger"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-1 m-1">
                                                    <button type="submit" class="btn btn-primary">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <div class="nk-block">
                        <div class="card shadown-none">
                            <div class="card-body">
                                <header>
                                    <h2 class="text-lg font-medium text-gray-900">
                                        {{ __('Update ChatGPT Key') }}
                                    </h2>
                                </header>
                                <div class="row g-3 gx-gs">
                                    <form method="post" action="{{ route('gptKey.update') }}" class="mt-6 space-y-6">
                                        @csrf
                                        <div class="col-md-12">
                                            <div class="form-group ">
                                                <div class="row col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label" for="model">Model Name</label>
                                                        <div class="form-control-wrap">
                                                            <select class="form-control" id="model" name="model">
                                                                <option value="gpt-4-1106-preview" @if($gptKey->
                                                                    model == 'gpt-4-1106-preview') selected @endif>
                                                                    gpt-4-1106-preview
                                                                </option>
                                                                <option value="gpt-4-0125-preview" @if($gptKey->
                                                                    model == 'gpt-4-0125-preview') selected @endif>
                                                                    gpt-4-0125-preview
                                                                </option>
                                                                <option value="gpt-4-turbo-preview" @if($gptKey->
                                                                    model == 'gpt-4-turbo-preview') selected @endif>
                                                                    gpt-4-turbo-preview
                                                                </option>
                                                                <option value="gpt-4" @if($gptKey->model == 'gpt-4')
                                                                    selected @endif>
                                                                    gpt-4</option>
                                                                <option value="gpt-4-0613" @if($gptKey->model ==
                                                                    'gpt-4-0613') selected @endif>
                                                                    gpt-4-0613</option>
                                                                <option value="gpt-4-32k" @if($gptKey->model ==
                                                                    'gpt-4-32k') selected @endif>
                                                                    gpt-4-32k</option>
                                                                <option value="gpt-4-32k-0613" @if($gptKey->model ==
                                                                    'gpt-4-32k-0613') selected @endif>
                                                                    gpt-4-32k-0613</option>
                                                                <option value="gpt-3.5-turbo-0125" @if($gptKey->
                                                                    model == 'gpt-3.5-turbo-0125') selected @endif>
                                                                    gpt-3.5-turbo-0125</option>
                                                                <option value="gpt-3.5-turbo" @if($gptKey->model ==
                                                                    'gpt-3.5-turbo') selected @endif>
                                                                    gpt-3.5-turbo</option>

                                                            </select>
                                                            <small class="text-danger"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label" for="key">Key</label>
                                                        <div class="form-control-wrap">
                                                            <input class="form-control" id="key" name="key" type="text"
                                                                value="{{ old('key',$gptKey->key) }}" />
                                                            <small class="text-danger"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label" for="prompt">Prompt</label>
                                                        <div class="form-control-wrap">
                                                            <textarea class="form-control" id="prompt" name="prompt">{{ $gptKey->prompt }}
                                                            </textarea>
                                                            <small class="text-danger"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row col-md-1 m-1">
                                                    <button type="submit" class="btn btn-primary">
                                                        Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</x-admin-layout>