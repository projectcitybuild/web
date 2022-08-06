@extends('v2.front.templates.master')

@section('title', 'Gallery')
@section('description', 'Community-uploaded images and screenshots')

@section('body')
    Upload a Photo

    <form method="post" action="{{ route('front.gallery.form.submit') }}">
        @csrf
        <input type="file" name="photo" class="form-control">
    </form>
@endsection
