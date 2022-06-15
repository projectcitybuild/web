@extends('admin.layouts.admin')

@section('title', 'Activity')

@section('body')
    <table class="table table-hover">
        <thead>
        <tr>
            <th>User</th>
            <th>Action</th>
            <th>When</th>
            <th>Subject</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($activities as $activity)
            <tr>
                <td>
                    <x-activity::model :model="$activity->causer"/>
                </td>
                <td>
                    <strong>
                        {{ $activity->subject_type }}
                        {{ $activity->description }}
                    </strong>
                    @if($activity->only_changed_attributes)
                        <br>
                        {{ join(', ', array_keys($activity->only_changed_attributes)) }}
                    @endif
                </td>
                <td>
                    <span title="{{ $activity->created_at }}" data-bs-toggle="tooltip">
                        {{ $activity->created_at->shortRelativeDiffForHumans() }}
                    </span>
                </td>
                <td>
                    <x-activity::model :model="$activity->subject"/>
                </td>
                <td>
                    <a href="{{ route('front.panel.activity.show', $activity) }}">
                        <i class="fas fa-eye me-1"></i>Show
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
