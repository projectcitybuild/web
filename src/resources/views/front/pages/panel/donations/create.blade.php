@extends('front.layouts.master')

@section('title', 'Donations - Staff Panel')

@section('contents')
    <div class="staff-panel">
        <h1>Create Donation</h1>

        @include('front.components.form-error')

        <div class="card card--no-padding">
            <div class="card__body">
                <form action="{{ route('front.panel.donations.store') }}" method="post">
                    @csrf
                    <table class="table table--divided">
                        <tr>
                            <td><label for="amount">Donation Amount</label></td>
                            <td>
                                <input type="amount" class="input-text" name="amount" id="amount" value="{{ old('amount', 0) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="account_id">Account ID</label></td>
                            <td>
                                <input type="account_id" class="input-text" name="account_id" id="account_id" value="{{ old('account_id', 0) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="created_at">Donation Date</label></td>
                            <td>
                                <input type="text" class="input-text" name="created_at" id="created_at" value="{{ old('created_at', now()) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="perks_end_at">Expiry Date</label></td>
                            <td>
                                <input type="text" class="input-text" name="perks_end_at" id="perks_end_at" value="{{ old('perks_end_at', now()) }}"><br />
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
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection
