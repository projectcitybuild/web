@extends('v2.front.templates.master')

@section('title', 'Page')
@section('description', 'Players listed on this page are currently banned on one or more servers on our game network')

@section('body')
    <header class="image-header">
        <div class="container">
            <h1>Page</h1>
        </div>
    </header>

    <main class="page settings">
        <div>
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
