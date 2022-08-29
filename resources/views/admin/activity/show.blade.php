@extends('admin.layouts.admin')

@section('title')
    Activity Log Entry
@endsection

@section('body')
    <div class="row">
        <div class="col-md-8">
            @switch($activity->event)
                @case('created')
                    <x-activity::attribute-list :changes="$activity->changes['attributes']" />
                    @break
                @case('updated')
                    @forelse($activity->changes_zipped as $attribute => $values)
                        <x-text-diff :attribute="$attribute" :old="$values['old']" :new="$values['new']"/>
                    @empty
                        <div class="text-center text-muted">
                            <em>No changes recorded</em>
                        </div>
                    @endforelse
                    @break
                @case('synced')
                    @forelse($activity->changes_zipped as $attribute => $values)
                        <x-text-diff
                            :attribute="$attribute"
                            :old="implode(PHP_EOL, $values['old'])"
                            :new="implode(PHP_EOL, $values['new'])"
                            :full-context="true"
                        />
                    @empty
                        <div class="text-center text-muted">
                            <em>No changes recorded</em>
                        </div>
                    @endforelse
                    @break
                @default
                    <div class="text-center text-muted">
                        <p>No additional data to show for event <em>{{ $activity->event }}</em></p>
                    </div>
            @endswitch


        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Metadata
                </div>
                <dl class="kv">
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Made by
                        </dt>
                        <dd class="col-md-9">
                            <x-activity::model :model="$activity->causer"/>
                        </dd>
                    </div>
                    <div class="row g-0">
                        <dt class="col-md-3">
                            Date
                        </dt>
                        <dd class="col-md-9">
                            {{ $activity->created_at }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
@endsection
