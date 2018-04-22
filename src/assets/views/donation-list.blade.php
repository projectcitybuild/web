@extends('layouts.master')

@section('title', 'Donator List')
@section('description', "Annual donation statistics and a list of all players who have donated to Project City Build")

@section('contents')

    <div class="card">
        <div class="card__header">
            <h2>Donation Statistics</h2>

            <div style="display: flex; flex-direction: row">
                <div class="card" style="margin-right: 1em">
                    <div class="card__body">
                        <h4>This Year</h4>
                        <h1 style="color: rgb({{ $figureColor }})">
                            ${{ number_format($thisYearSum, 2) }}
                        </h1>

                        Average: ${{ number_format($thisYearAvg, 2) }}<br>
                        Donations: {{ $thisYearCount }}
                    </div>
                </div>

                <div class="card" style="margin-right: 1em">
                    <div class="card__body">
                        <h4>Last Year</h4>
                        <h1>${{ number_format($lastYearSum, 2) }}</h1>

                        Average: ${{ number_format($lastYearAvg, 2) }}<br>
                        Donations: {{ $lastYearCount }}
                    </div>
                </div>

                <div class="card">
                    <div class="card__body">
                        <h4>Overall</h4>
                        Highest: <br>
                        Lowest: 
                    </div>
                </div>
            </div>
        </div>

        <div class="card__body">
            <h2>Donations</h2>
            <table class="table table--striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Amount Donated</th>
                        <th>Date of Donation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donations as $donation)
                    <tr>
                        <td>{{ $loop->count - $loop->index }}</td>
                        <td>{{ $donation->account_id }}</td>
                        <td>${{ number_format($donation->amount, 2) }}</td>
                        <td>{{ $donation->created_at->format('D, jS \o\f F, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

@endsection