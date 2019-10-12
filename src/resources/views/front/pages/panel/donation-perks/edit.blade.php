@extends('front.layouts.master')

@section('title', 'Donation Perks - Staff Panel')

@section('contents')
    <div class="staff-panel">
        <h1>Edit Donation Perk</h1>

        @include('front.components.form-error')

        <div class="card">
            <div class="card__body card--narrow">
                <form method="post" action="{{ route('front.panel.donation-perks.destroy', $perk) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button button--secondary button--bordered"><i class="fas fa-trash"></i> Delete</button>
                </form>
            </div>
        </div>

        <form action="{{ route('front.panel.donation-perks.update', $perk) }}" method="post">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card__body">
                    <table class="table table--divided">
                        <tr>
                            <td><label for="account_id">Donation ID</label></td>
                            <td>
                                <input type="text" class="input-text" name="donation_id" id="donation_id" value="{{ old('donation_id', $perk->donation_id) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="account_id">Account ID</label></td>
                            <td>
                                <input type="text" class="input-text" name="account_id" id="account_id" value="{{ old('account_id', $perk->account_id) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="created_at">Start Date</label></td>
                            <td>
                                <input type="text" class="input-text" name="created_at" id="created_at" value="{{ old('created_at', $perk->created_at) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="expires_at">Expiry Date</label></td>
                            <td>
                                <input type="text" class="input-text" name="expires_at" id="expires_at" value="{{ old('expires_at', $perk->expires_at) }}"><br />
                                (Required if not lifetime)
                            </td>
                        </tr>
                        <tr>
                            <td><label for="is_active">Active</label></td>
                            <td>
                                <input type="checkbox" name="is_active" id="is_active" value="1"{{ old('is_active', $perk->is_active) ? ' checked' : '' }}>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="is_lifetime_perks">Lifetime</label></td>
                            <td>
                                <input type="checkbox" name="is_lifetime_perks" id="is_lifetime_perks" value="1"{{ old('is_lifetime_perks', $perk->is_lifetime_perks) ? ' checked' : '' }}>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card__footer">
                    <button type="submit" class="button button--primary button--large"><i class="fas fa-check"></i> Save</button>
                </div>
            </div>
        </form>

    </div>
@endsection
