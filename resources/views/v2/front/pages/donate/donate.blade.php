@extends('v2.front.templates.master')

@section('title', 'Donate')
@section('description', "Help keep us online by donating")

@section('body')
    <form action="{{ route('front.donations.moveToCheckout') }}" method="POST">
        @csrf
        <input type="hidden" name="price_id" value="price_1JJL5mAtUyfM4v5IJNHp1Tk2" />
        <input type="hidden" name="quantity" value="1" />
        <input type="hidden" name="is_subscription" value="0" />
        <button type="submit">One Time</button>
    </form>
    <form action="{{ route('front.donations.moveToCheckout') }}" method="POST">
        @csrf
        <input type="hidden" name="price_id" value="price_1JJL5mAtUyfM4v5ISwJrrVur" />
        <input type="hidden" name="quantity" value="1" />
        <input type="hidden" name="is_subscription" value="1" />
        <button type="submit">Subscription</button>
    </form>
@endsection
