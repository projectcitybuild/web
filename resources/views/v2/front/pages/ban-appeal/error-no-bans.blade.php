@extends('v2.front.pages.ban-appeal._layout')

@section('col-2')
    <div class="contents__section">
        <div class="alert alert--error">
            <h2><i class="fas fa-exclamation-circle"></i> Account not banned</h2>
            The user <strong>{{ $player->getBanReadableName() }}</strong> is not currently banned.
        </div>
    </div>
@endsection
