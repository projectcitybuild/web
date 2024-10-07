@extends('front.pages.ban-appeal._layout-form')

@section('col-2')
    <div class="contents__section">
        <div class="alert alert--error">
            <h2><i class="fas fa-exclamation-circle"></i> Account not banned</h2>
            The user <strong>{{ $player->alias }}</strong> is not currently banned.
        </div>
    </div>
@endsection
