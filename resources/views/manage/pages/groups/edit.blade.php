@extends('manage.layouts.admin')

@section('title', 'Edit Group #' . $group->getKey())

@section('toolbar')
    <div class="btn-toolbar">
        <form method="post" action="{{ route('manage.groups.destroy', $group) }}">
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
            <form action="{{ route('manage.groups.update', $group) }}" method="post">
                @csrf
                @method('PUT')

                @include('manage.pages.groups._form', ['buttonText' => 'Save'])
            </form>
        </div>
    </div>
@endsection
