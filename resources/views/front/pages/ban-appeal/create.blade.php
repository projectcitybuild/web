@extends('front.pages.ban-appeal._layout-form')

@section('col-2')
    <div class="contents__section">
        <h2>Appeals Process</h2>
        <ol>
            <li>Submit an Application using the below form</li>
            <li>Applications are reviewed by the staff member that banned you</li>
            <li>You may be unbanned, your ban converted to a temporary ban, or unbanned</li>
        </ol>

        <strong>Before you appeal, please read the ban details below. Appeals which do not relate to the ban reason are
            likely to be denied.</strong>
    </div>
    <div class="contents__section">
        <h2>Ban History</h2>
        <p>Below is a list of your current ban and any previous bans.</p>
        @include('front.pages.ban-appeal._ban-history')
    </div>
    <div class="contents__section">
        <h2>Appeal Form</h2>

        <form method="post" action="{{ route('front.appeal.submit', $activegamePlayerBan) }}" id="form" class="form">
            @csrf
            @include('front.components.form-error')
            @unless($accountVerified)
                <div class="form-row">
                    <label for="email">Email Address</label>
                    <input type="text"
                           class="textfield"
                           name="email"
                           id="email"
                           placeholder="steve@minecraft.net"
                           value="{{ old('email') }}"
                           aria-describedby="emailHelp"
                    >
                    <p class="help-text" id="emailHelp">Please check this carefully, updates to your appeal will be sent
                        to this address.</p>
                </div>
            @endunless
            <div class="form-row">
                <label for="explanation">Reason to be Unbanned</label>
                <textarea
                    class="textfield"
                    name="explanation"
                    id="explanation"
                    rows="5"
                    placeholder="e.g. I accidentally broke that house. Sorry!"
                >{{ old('explanation') }}</textarea>
            </div>

            <x-front::button
                class="g-recaptcha button button--filled button--block"
                type="submit"
            >
                <i class="fas fa-check"></i> Submit
            </x-shared::button>
        </form>
    </div>
@endsection
