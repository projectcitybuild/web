@extends('admin.layouts.admin')

@section('title')
    Edit {{ $account->username ?? $account->email }}
@endsection

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')
            <form action="{{ route('manage.accounts.update', $account) }}" method="post">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <label for="email" class="col-sm-2 col-form-label horizontal-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $account->email) }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="username" class="col-sm-2 col-form-label horizontal-label">Username</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $account->username) }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-10 ms-auto">
                        <span class="text-muted">Any current email change requests will be cancelled.</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-10 ms-auto">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
