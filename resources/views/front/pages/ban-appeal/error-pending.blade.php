@extends('front.pages.ban-appeal._layout-form')

@section('col-2')
    <div class="contents__section">
        <div class="alert alert--error">
            <h2>Appeal Already Created</h2>
            @if($existingAppeal->is_account_verified)
                You already have an <a href="{{ route('manage.ban-appeals.show', $banAppeal) }}">appeal in progress</a>.
            @else
                You already have an appeal in progress. Click the link in the appeal confirmation email to check
                progress.
            @endif
        </div>
    </div>
@endsection
