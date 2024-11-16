@extends('manage.layouts.admin')

@section('title', 'Groups')

@section('toolbar')
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('manage.groups.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Create</a>
    </div>
@endsection

@section('body')
    <div class="table-responsive">
        <table class="table highlight-linked">
            <thead>
            <tr>
                <th>Name</th>
                <th>Members</th>
                <th>Alias</th>
                <th>Group Type</th>
                <th>Minecraft Name</th>
                <th>Minecraft Display Name</th>
                <th>Minecraft Hover Text</th>
                <th>Display Priority</th>
                <th>Panel Access?</th>
            </tr>
            </thead>
            <tbody>
            @foreach($groups as $group)
                <tr id="group-{{ $group->getKey() }}">
                    <td>
                        <a href="{{ route('manage.groups.edit', $group) }}">{{ $group->name }}</a></td>
                    <td>
                        <a href="{{ route('manage.groups.accounts', $group) }}">{{ number_format($group->accounts_count) }}</a>
                    </td>
                    <td>{{ $group->alias }}</td>
                    <td>
                        @if ($group->group_type != "")
                            {{ $group->group_type }}
                        @endif
                    </td>
                    <td>
                        @if ($group->minecraft_name != "")
                            {{ $group->minecraft_name }}
                        @endif
                    </td>
                    <td>
                        @if ($group->minecraft_display_name != "")
                            {{ $group->minecraft_display_name }}
                        @endif
                    </td>
                    <td>
                        @if ($group->minecraft_hover_text != "")
                            {{ $group->minecraft_hover_text }}
                        @endif
                    </td>
                    <td>
                        @if ($group->display_priority != "")
                            {{ $group->display_priority }}
                        @endif
                    </td>
                    <td><x-manage::bs.fa-boolean :data="$group->can_access_panel" false-class="text-muted" /></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
