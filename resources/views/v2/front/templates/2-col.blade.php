@extends('v2.front.templates.master')

@section('body')
    <main class="page form">
        <section class="overview">
            <h1>@yield('heading')</h1>

            <div class="overview__description">
                @yield('col-1')
            </div>
        </section>

        <section class="contents">
            @yield('col-2')
        </section>
    </main>
@endsection
