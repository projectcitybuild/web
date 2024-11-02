@extends('manage.layouts.admin')

@section('title')
    Edit Minecraft Player {{ $minecraftPlayer->alias ?? $minecraftPlayer->uuid }}
@endsection

@section('body')
    <div class="row">
        <div class="col-md-8">
            @include('manage._errors')

            <form action="{{ route('manage.minecraft-players.update', $minecraftPlayer) }}" method="post">
            @csrf
            @method('PUT')
                <div class="row mb-3">
                    <label for="account_id" class="col-sm-3 col-form-label horizontal-label">Account ID</label>
                    <div class="col-sm-9">
                        <x-manage::account-picker :account="$minecraftPlayer->account" />
                        <div id="accountIdHelp" class="form-text">The player can be unassigned by emptying this field.</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-9 ms-auto">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
