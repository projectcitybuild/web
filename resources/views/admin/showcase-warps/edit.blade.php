@extends('admin.layouts.admin')

@section('title', 'Edit Showcase Warp #' . $warp->getKey())

@section('toolbar')
    <div class="btn-toolbar">
        <form method="post" action="{{ route('front.panel.showcase-warps.destroy', $warp) }}">
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
            <form action="{{ route('front.panel.showcase-warps.update', $warp) }}" method="post">
                @csrf
                @method('PUT')

                @include('admin.showcase-warps._form', ['buttonText' => 'Save'])
            </form>
        </div>
    </div>
@endsection
