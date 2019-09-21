@extends('front.layouts.master')

@section('title', 'Staff Panel')
@section('description', 'Players listed on this page are currently banned on one or more servers on our game network')

@section('contents')

    <div class="staff-panel">
        <h1>Staff Panel</h1>

        <div class="card">
            <div class="card__body">
                <ul>
                    <li><a href="{{ route('front.panel.accounts.index') }}">Accounts</a></li>
                </ul>
            </div>
        </div>
    </div>

@endsection
