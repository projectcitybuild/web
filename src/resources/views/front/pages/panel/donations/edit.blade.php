@extends('front.layouts.master')

@section('title', 'Donations - Staff Panel')

@section('contents')
    <div class="staff-panel">
        <h1>Edit Donation</h1>

        @include('front.components.form-error')

        <div class="card card--no-padding">
            <div class="card__body">
                <form action="{{ route('front.panel.donations.update', $donation) }}" method="post">
                    @csrf
                    @method('PUT')
                    <table class="table table--divided">
                        <tr>
                            <td><label for="amount">Donation Amount</label></td>
                            <td>
                                <input type="amount" class="input-text" name="amount" id="amount" value="{{ old('amount', $donation->amount) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="account_id">Account ID</label></td>
                            <td>
                                <input type="account_id" class="input-text" name="account_id" id="account_id" value="{{ old('account_id', $donation->account_id) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="created_at">Donation Date</label></td>
                            <td>
                                <input type="text" class="input-text" name="created_at" id="created_at" value="{{ old('created_at', $donation->created_at) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="perks_end_at">Expiry Date</label></td>
                            <td>
                                <input type="text" class="input-text" name="perks_end_at" id="perks_end_at" value="{{ old('perks_end_at', $donation->perks_end_at) }}"><br />
                                (Required if not lifetime)
                            </td>
                        </tr>
                        <tr>
                            <td><label for="is_active">Active</label></td>
                            <td>
                                <input type="checkbox" name="is_active" id="is_active" value="1"{{ old('is_active', $donation->is_active) ? ' checked' : '' }}>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="is_lifetime_perks">Lifetime</label></td>
                            <td>
                                <input type="checkbox" name="is_lifetime_perks" id="is_lifetime_perks" value="1"{{ old('is_lifetime_perks', $donation->is_lifetime_perks) ? ' checked' : '' }}>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button type="submit" class="button button--primary button--large"><i class="fas fa-check"></i> Save</button>

                                <form method="post" action="{{ route('front.panel.donations.destroy', $donation) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button button--large">Delete</button>
                                </form>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection
