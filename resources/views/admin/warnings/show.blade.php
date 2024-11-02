@extends('admin.layouts.admin')

@section('title')
    Warning #{{ $warning->id }}
@endsection

@section('toolbar')
    <a href="{{ route('manage.warnings.edit', $warning) }}" class="btn btn-outline-primary btn-md py-0">
        <i class="fas fa-pencil-alt"></i> Edit
    </a>
@endsection

@section('body')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-default">
                <div class="card-header d-flex justify-content-between">
                    <span>Context</span>
                </div>
                <dl class="kv">
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Reason
                        </dt>
                        <dd class="col-md-9">
                            {{ $warning->reason }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Additional Info
                        </dt>
                        <dd class="col-md-9">
                            <div>
                                {{ $warning->additional_info ?? '-' }}
                            </div>
                            <div class="text-muted">
                                <br />
                                (This field is not shown to the user. It's for recording purposes)
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-default">
                <div class="card-header d-flex justify-content-between">
                    <span>Details</span>
                </div>
                <dl class="kv">
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Player
                        </dt>
                        <dd class="col-md-9">
                            <div>
                                <a href="{{ route('manage.minecraft-players.show', $warning->warnedPlayer) }}">
                                    {{ $warning->warnedPlayer->alias ?? '(No Alias)' }}
                                </a>
                            </div>
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Warned By
                        </dt>
                        <dd class="col-md-9">
                            <div>
                                <a href="{{ route('manage.minecraft-players.show', $warning->warnerPlayer) }}">
                                    {{ $warning->warnerPlayer->alias ?? '(No Alias)' }}
                                </a>
                            </div>
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Created At
                        </dt>
                        <dd class="col-md-9">
                            {{ $warning->created_at }}
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Updated At
                        </dt>
                        <dd class="col-md-9">
                            {{ $warning->updated_at }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="card card-default">
                <div class="card-header d-flex justify-content-between">
                    <span>Acknowledgement</span>
                </div>
                <dl class="kv">
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Is Acknowledged?
                        </dt>
                        <dd class="col-md-9">
                            <div>
                                @if ($warning->isAcknowledged)
                                    Yes ({{ $warning->acknowledged_at }})
                                @else
                                    No
                                @endif
                            </div>
                            <div class="text-muted">
                                Whether the player has pressed a button to acknowledge they have seen the warning
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
@endsection
