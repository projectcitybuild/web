@extends('front.layouts.master')

@section('title', 'Donations - Staff Panel')

@section('contents')
    <div class="staff-panel">
        <h1>Edit Donation</h1>

        @include('front.components.form-error')


        <div class="card">
            <div class="card__body">
                <form method="post" action="{{ route('front.panel.donations.destroy', $donation) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button button--secondary button--bordered"><i class="fas fa-trash"></i> Delete</button>
                </form>
            </div>
        </div>

        <form action="{{ route('front.panel.donations.update', $donation) }}" method="post">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card__body">
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
                    </table>
                </div>

                <div class="card__footer">
                    <button type="submit" class="button button--primary button--large"><i class="fas fa-check"></i> Save</button>
                </div>
            </div>
        </form>
    </div>
@endsection
