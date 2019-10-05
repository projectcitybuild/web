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
                            <td><label for="created_at">Donation Date</label></td>
                            <td>
                                <input type="text" class="input-text" name="created_at" id="created_at" value="{{ old('active', $donation->created_at) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="expires_at">Expiry Date</label></td>
                            <td>
                                <input type="text" class="input-text" name="expires_at" id="expires_at" value="{{ old('active', $donation->expires_at) }}">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="active">Active</label></td>
                            <td>
                                <input type="checkbox" name="active" id="active" value="1"{{ old('active', $donation->is_active) ? ' checked' : '' }}>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="lifetime">Lifetime</label></td>
                            <td>
                                <input type="checkbox" name="lifetime" id="lifetime" value="1"{{ old('lifetime', $donation->is_lifetime_perks ? ' checked' : '') }}>
                            </td>
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
