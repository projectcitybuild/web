@extends('admin.layouts.admin')

@section('title', 'Create Warp')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')
            <form action="{{ route('front.panel.minecraft.warps.store') }}" method="post">
                @csrf

                @include('admin.minecraft-warps._form', ['buttonText' => 'Create'])
            </form>
        </div>
    </div>
@endsection
