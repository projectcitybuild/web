@extends('admin.layouts.admin')

@section('title', 'Create MC Player')

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('admin._errors')

            <form action="{{ route('front.panel.minecraft-players.store') }}" method="post">
                @csrf

                <div class="row mb-3">
                    <label for="uuid" class="col-sm-3 col-form-label horizontal-label">UUID</label>
                    <div class="col-sm-9">
                        <input type="text" id="uuid" name="uuid" class="form-control" value="{{ old('uuid') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="account_id" class="col-sm-3 col-form-label horizontal-label">Account ID</label>
                    <div class="col-sm-9">
                        <select name="account_id" id="account_id" aria-describedby="accountIdHelp" data-pcb-user-picker></select>
                        <div id="accountIdHelp" class="form-text">Optional. Player will not be assigned to a user if not filled in.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-9 ms-auto">
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
