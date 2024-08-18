@extends('front.templates.2-col')

@section('title', 'Your Ban Appeal')
@section('heading', 'Ban Appeal')
@section('description', 'Check the status of your ban appeal')

@section('col-1')
    <p>
        View the status of your ban appeal. You will be emailed with any updates.
    </p>
    <p>
        Please note that you cannot submit another appeal until this appeal has been resolved.
    </p>
@endsection

@section('col-2')
    <div class="contents__section">
        @include('front.components.form-error')
        @switch($banAppeal->status)
            @case(\App\Domains\BanAppeals\Entities\BanAppealStatus::PENDING)
                <div class="alert alert--info">
                    <h2><i class="fas fa-hourglass"></i> Appeal Pending</h2>
                    Please wait whilst your appeal is reviewed by staff. This usually happens within 48 hours, but may
                    take
                    longer in some cases.
                </div>
                @break
            @case(\App\Domains\BanAppeals\Entities\BanAppealStatus::ACCEPTED_UNBAN)
                <div class="alert alert--success">
                    <h2><i class="fas fa-check"></i> Appeal Accepted</h2>
                    Your ban appeal has been accepted.<br>
                    <strong>You must read the response from staff, as it may contain important information to prevent
                        you being banned in future.</strong>
                </div>
                @break
            @case(\App\Domains\BanAppeals\Entities\BanAppealStatus::ACCEPTED_TEMPBAN)
                <div class="alert alert--warning">
                    <h2><i class="fas fa-hourglass-half"></i> Ban reduced</h2>
                    <p>Your appeal has been considered, and your ban has been reduced to a temporary ban.</p>
                    <p>You will be unbanned on {{ $banAppeal->gamePlayerBan->expires_at }}.</p>
                </div>
                @break
            @case(\App\Domains\BanAppeals\Entities\BanAppealStatus::DENIED)
                <div class="alert alert--error">
                    <h2><i class="fas fa-times"></i> Appeal Denied</h2>
                    Sorry, your appeal was denied. The response from staff is shown below.<br>
                    Please read and consider this before making another appeal.
                </div>
        @endswitch
    </div>
    <div class="contents__section">
        <h2>Your Appeal</h2>
        <div class="messages">
            <div class="message message--left">
                <div class="message-avatar">
                    <img src="https://minotar.net/helm/{{ $banAppeal->gamePlayerBan->bannedPlayer?->uuid }}/32"
                         width="32"/>
                </div>
                <div class="message-comment">
                    <div class="message-text">
                        {{ $banAppeal->explanation }}
                    </div>
                    <div class="message-date">
                        You &bull; {{ $banAppeal->created_at }}
                    </div>
                </div>
            </div>
            @if($banAppeal->status->isDecided())
                <div class="message message--right message--distinguish">
                    <div class="message-comment">
                        <div class="message-text">
                            @empty($banAppeal->decision_note)
                                No decision message provided.
                            @else
                                {{ $banAppeal->decision_note }}
                            @endif
                        </div>
                        <div class="message-date">
                            {{$banAppeal->deciderAccount?->username }} &bull; {{ $banAppeal->decided_at }}
                        </div>
                    </div>
                    <div class="message-avatar">
                        <img src="https://minotar.net/helm/{{ $banAppeal->deciderAccount?->uuid }}/32" width="32"/>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
