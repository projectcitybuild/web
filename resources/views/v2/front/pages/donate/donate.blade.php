@extends('v2.front.templates.master')

@section('title', 'Donate')
@section('description', "Help keep us online by donating")

@section('body')
    <form action="{{ route('front.donations.create') }}" method="POST">
        @csrf
        <input type="hidden" name="price_id" value="price_1JJL5mAtUyfM4v5IJNHp1Tk2" />
        <button type="submit">Checkout</button>
    </form>
@endsection
