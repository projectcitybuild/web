@extends('front.layouts.master')

@section('title', 'Donation Perks - Staff Panel')

@section('contents')
    <div class="staff-panel">
        <h1>Create Donation Perk</h1>

        @include('front.components.form-error')

        <div class="card card--no-padding">
            <div class="card__body">
                <form action="{{ route('front.panel.donation-perks.store') }}" method="post">
                    @csrf
                    <table class="table table--divided">
                        <tr>
                            <td><label for="donation_id">Donation ID</label></td>
                            <td>
                                <input type="text" class="input-text" name="donation_id" id="donation_id" value="{{ old('donation_id', 0) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="account_id">Account ID</label></td>
                            <td>
                                <input type="text" class="input-text" name="account_id" id="account_id" value="{{ old('account_id', 0) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="created_at">Start Date</label></td>
                            <td>
                                <input type="text" class="input-text" name="created_at" id="created_at" value="{{ old('created_at', now()) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="expires_at">Expiry Date</label></td>
                            <td>
                                <input type="text" class="input-text" name="expires_at" id="expires_at" value="{{ old('expires_at', now()->addMonth()) }}"><br />
                                (Required if not lifetime)
                            </td>
                        </tr>
                        <tr>
                            <td><label for="is_active">Active</label></td>
                            <td>
                                <input type="checkbox" name="is_active" id="is_active" value="1"{{ old('is_active', true) ? ' checked' : '' }}>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="is_lifetime_perks">Lifetime</label></td>
                            <td>
                                <input type="checkbox" name="is_lifetime_perks" id="is_lifetime_perks" value="1"{{ old('is_lifetime_perks', false) ? ' checked' : '' }}>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><button type="submit" class="button button--primary">Create</button></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>(Creating a donation perk does <strong>NOT</strong> automatically assign an account to the Donator group)</td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection
