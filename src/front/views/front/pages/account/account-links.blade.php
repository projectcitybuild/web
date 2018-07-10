@extends('front.layouts.master')

@section('title', 'Account Links')
@section('description', '')

@section('contents')

    <div class="contents__body">

        <div class="card card--divided">
            <div class="card__body card__body--padded">
                <h1>Connected Social Accounts</h1>
                <span class="header-description">Linked social accounts for logging-in</span>
            </div>
            <div class="card__body card__body--padded">

            </div>

        </div>

    </div>

    <div class="contents__sidebar">
        <div class="card card--no-padding">
            <div class="card__body">

                <ul class="sidemenu">
                    <li>
                        <a href="{{ route('front.account.settings') }}">
                            <span class="fa-stack fa-2x">
                                <i class="fas fa-square fa-stack-2x"></i>
                                <i class="fas fa-lock fa-stack-1x fa-inverse"></i>
                            </span>
                            Account Security
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('front.account.social') }}">
                            <span class="fa-stack fa-2x">
                                <i class="fas fa-square fa-stack-2x"></i>
                                <i class="fab fa-facebook fa-stack-1x fa-inverse"></i>
                            </span>
                            Connected Social Accounts
                        </a>
                    </li>
                </ul>

            </div>
        </div>
    </div>


@endsection