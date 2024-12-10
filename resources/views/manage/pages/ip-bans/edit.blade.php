@extends('manage.layouts.admin')

@section('title', 'Edit IP Ban #' . $ban->getKey())

@section('toolbar')
    <div class="btn-toolbar">
        <form method="post" action="{{ route('manage.ip-bans.destroy', $ban) }}">
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
            <form action="{{ route('manage.ip-bans.update', $ban) }}" method="post">
                @csrf
                @method('PUT')

                @include('manage.pages.ip-bans._form', ['buttonText' => 'Save'])
            </form>
        </div>
    </div>
@endsection
