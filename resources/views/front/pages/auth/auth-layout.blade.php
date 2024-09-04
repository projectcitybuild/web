@extends('front.root-layout')

@section('body')
    <main class="grid grid-cols-1 md:grid-cols-2 h-screen">
        <div class="bg-gray-50 flex flex-row justify-center">
            <div class="p-6 md:p-12 max-w-screen-sm flex-grow flex flex-col">
                <div class="flex-grow min-h-24">
                    <x-logo />
                </div>
                <div>
                    @yield('content')
                </div>
                <div class="flex-grow"></div>
            </div>
        </div>

        <div class="
            hidden md:flex
            bg-gray-100
            bg-cover bg-no-repeat bg-center
            bg-[url('/resources/images/login_bg.png')]
            flex-row justify-end
        ">
            <span class="bg-white bg-opacity-10 text-gray-300 py-1 px-2 text-xs self-end">
                Alderrdeen - Build by <strong>Wolfy</strong>
            </span>
        </div>
    </main>
@endsection
