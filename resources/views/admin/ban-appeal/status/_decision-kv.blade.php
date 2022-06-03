<dl class="kv border-top">
    <div class="row g-0">
        <dt class="col-md-3">
            Decided At
        </dt>
        <dd class="col-md-9">
            {{ $banAppeal->decided_at }}
        </dd>
    </div>
    <div class="row g-0">
        <dt class="col-md-3">
            Decided By
        </dt>
        <dd class="col-md-9">
            <a href="{{ route('front.panel.accounts.show', $banAppeal->deciderAccount) }}">
                {{ $banAppeal->deciderAccount->username }}
            </a>
        </dd>
    </div>
    <div class="row g-0">
        <dt class="col-md-3">
            Decision Reason
        </dt>
        <dd class="col-md-9">
            <em>{{ $banAppeal->decision_note }}</em>
        </dd>
    </div>
</dl>
