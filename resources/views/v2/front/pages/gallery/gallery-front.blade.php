@extends('v2.front.templates.master')

@section('title', 'Gallery')
@section('description', 'Community-uploaded images and screenshots')

@section('body')
    <h1>Community Gallery</h1>

    Photos uploaded by our community

    @auth
        <a href="{{ route('front.gallery.form') }}">Upload Photo</a>
    @endauth

    TODO: masonry gallery
@endsection
