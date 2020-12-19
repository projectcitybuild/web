@extends('front.layouts.master')

@section('title', 'Accounts - Staff Panel')

@section('contents')
    <div class="staff-panel">
        <h1>Edit {{ $account->username }}</h1>

        @include('front.components.form-error')

        <div class="card card--no-padding">
            <div class="card__body">
                <form action="{{ route('front.panel.accounts.update', $account) }}" method="post">
                    @csrf
                    @method('PUT')
                    <table class="table table--divided">
                        <tr>
                            <td><label for="email">Email</label></td>
                            <td>
                                <input type="email" class="input-text" name="email" id="email" value="{{ old('email', $account->email) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="username">Username</label></td>
                            <td>
                                <input type="text" class="input-text" name="username" id="username" value="{{ old('username', $account->username) }}">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Any current email change requests will be cancelled.</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><button type="submit" class="button button--primary">Save</button></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection
