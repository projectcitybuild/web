@extends('admin.layouts.admin')

@section('title', 'Edit Warp #' . $warp->getKey())

@section('toolbar')
    <div class="btn-toolbar">
        <form method="post" action="{{ route('manage.minecraft.warps.destroy', $warp) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Delete</button>
        </form>
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')
            <form action="{{ route('manage.minecraft.warps.update', $warp) }}" method="post">
                @csrf
                @method('PUT')

                @include('admin.minecraft-warps._form', ['buttonText' => 'Save'])
            </form>
        </div>
    </div>
@endsection
