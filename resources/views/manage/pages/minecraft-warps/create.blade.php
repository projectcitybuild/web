@extends('manage.layouts.admin')

@section('title', 'Create Warp')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')
            <form action="{{ route('manage.minecraft.warps.store') }}" method="post">
                @csrf

                @include('manage.pages.minecraft-warps._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
