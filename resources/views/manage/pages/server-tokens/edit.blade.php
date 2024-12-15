@extends('manage.layouts.admin')

@section('title', 'Edit Server Token #' . $serverToken->getKey())

@section('toolbar')
    <div class="btn-toolbar">
        <form method="post" action="{{ route('manage.server-tokens.destroy', $serverToken) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Delete</button>
        </form>
    </div>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')
            <form action="{{ route('manage.server-tokens.update', $serverToken) }}" method="post">
                @csrf
                @method('PUT')

                @include('manage.pages.server-tokens._form', ['buttonText' => 'Save'])
            </form>
        </div>
    </div>
@endsection
