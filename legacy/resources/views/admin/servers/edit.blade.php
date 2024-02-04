@extends('admin.layouts.admin')

@section('title', 'Edit Server #' . $server->getKey())

@section('toolbar')
    <div class="btn-toolbar">
        <form method="post" action="{{ route('front.panel.servers.destroy', $server) }}">
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
            <form action="{{ route('front.panel.servers.update', $server) }}" method="post">
                @csrf
                @method('PUT')

                @include('admin.servers._form', ['buttonText' => 'Save'])
            </form>
        </div>
    </div>
@endsection
