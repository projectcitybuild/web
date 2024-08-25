@extends('front.root')

@section('title', 'Sign In - Project City Build')
@section('meta_title', 'Sign In - Project City Build')

@section('body')
    <div class="grid grid-cols-1 md:grid-cols-2 h-screen">
        <div class="bg-gray-50 p-6 flex justify-center items-center">
            <div class="max-w-screen-sm flex-grow flex flex-col">
                <h1 class="text-4xl font-bold text-gray-900 mb-12">Sign In</h1>

                <label for="email" class="text-md font-bold {{ $errors->any() ? 'text-red-500' : '' }}">Email Address</label>
                <input
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                        {{ $errors->any() ? 'border-red-500' : '' }}
                    "
                    id="email"
                    name="email"
                    type="email"
                    placeholder="you@projectcitybuild.com"
                    value="{{ old('email') }}"
                />

                <label for="password" class="text-md font-bold mt-6 {{ $errors->any() ? 'text-red-500' : '' }}">Password</label>
                <input
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                        {{ $errors->any() ? 'border-red-500' : '' }}
                    "
                    id="password"
                    name="password"
                    type="password"
                    placeholder="****************"
                    value="{{ old('password') }}"
                />

                <div class="flex justify-between mt-6">
                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            name="remember_me"
                            id="remember_me"
                            class="
                            rounded-sm
                            bg-gray-300 border-0
                            checked:bg-gray-900
                        "
                            checked
                        > Stay logged in
                    </div>

                    <a href="{{ route('front.password-reset.create') }}" class="text-sm text-blue-600">Forgot your password?</a>
                </div>


                <input
                    type="submit"
                    class="
                        text-sm sm:text-lg text-gray-50
                        bg-gray-900 rounded-md shadow-lg
                        hover:bg-gray-800
                        px-6 py-4 mt-12
                    "
                    value="Sign In"
                />

                <div class="mt-12 m-auto">
                    <span class="text-gray-500">
                        Don't have an account? <a href="{{ route('front.register') }}" class="text-blue-600 font-bold">Register for free</a>
                    </span>
                </div>
            </div>
        </div>
        <div class="
            hidden md:flex
            bg-cover bg-no-repeat bg-center
            bg-[url('/resources/images/login_bg.png')]
            flex-row justify-end
        ">
            <span class="bg-white bg-opacity-10 text-gray-300 py-1 px-2 text-xs self-end">
                Alderrdeen - Build by <strong>Wolfy</strong>
            </span>
        </div>
    </div>

{{--    <main class="page login">--}}
{{--        <div class="container">--}}
{{--            <section class="login__dialog login__sign-in">--}}
{{--                <h1>Sign In to PCB</h1>--}}

{{--                <form method="post" action="{{ route('front.login.submit') }}">--}}
{{--                    @csrf--}}

{{--                    @include('front.components.form-error')--}}

{{--                    @if(Session::get('mfa_removed', false))--}}
{{--                        <div class="alert alert--success">--}}
{{--                            <h2><i class="fas fa-check"></i> 2FA Reset</h2>--}}
{{--                            2FA has been removed from your account, please sign in again.--}}
{{--                        </div>--}}
{{--                    @endif--}}

{{--                    @if(session()->has('success'))--}}
{{--                        <div class="alert alert--success">--}}
{{--                            <h2><i class="fas fa-check"></i> Success</h2>--}}
{{--                            {{ session()->get('success') }}--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                </form>--}}
{{--            </section>--}}
{{--        </div>--}}
{{--    </main>--}}
@endsection
