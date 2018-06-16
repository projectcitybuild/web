@extends('layouts.master')

@section('title', 'Create an Account')
@section('description', 'Create a PCB account to create forum posts, access personal player statistics and more.')

@section('contents')

    <div class="card">
        <div class="card__body card__body--padded">
            <h1>Manage Game Accounts</h1>

            By linking your game accounts to your PCB account, your server rank will automatically synchronize with your website rank.
            Furthermore, you gain access to extra features such as player stats and more. 

            <h3>Minecraft</h3>
            <form method="POST" action="{{ route('front.account.games.save') }}">
                @csrf

                <input type="text" 
                       name="minecraft_uuid" 
                       placeholder="069a79f4-44e9-4726-a5be-fca90e38aaf5" 
                       value="{{ old('minecraft_uuid', $minecraft['uuid']) }}"
                       />

                <button type="submit" name="minecraft">
                    Save Minecraft UUID
                </button>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Alias</th>
                        <th>Status</th>
                        <th>Stopped using on</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($minecraft['aliases'] as $alias)
                    <tr>
                        <td></td>
                        <td>{{ $alias->alias }}</td>
                        <td>{{ $alias->registered_at === null ? 'In Use' : '-' }}</td>
                        <td>{{ $alias->registered_at === null ? '-' : $alias->registered_at->format('l, jS \o\f F, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

@endsection