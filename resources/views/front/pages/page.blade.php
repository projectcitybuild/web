@extends('front.templates.master')

@section('title', $page->title . ' - Project City Build')
@section('description', 'Players listed on this page are currently banned on one or more servers on our game network')

@section('body')
    <header class="custom-page">
        <div class="container">
            <h1>{{ $page->title }}</h1>

            <span class="description">{{ $page->description }}</span>
        </div>
    </header>

    <main class="page custom-page">
        <div class="container">
            @php
                $converter = new League\CommonMark\GithubFlavoredMarkdownConverter([
                    'html_input' => 'strip',
                    'allow_unsafe_links' => false,
                ]);

                echo $converter->convert($page->contents);
            @endphp
        </div>
    </main>
@endsection
