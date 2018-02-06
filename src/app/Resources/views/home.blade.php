@extends('layouts.sidebar-layout')

@section('left')

    @if(isset($announcements))
        @foreach($announcements as $announcement)
            <article class="article card">
                <div class="article__container">
                    <h2 class="article__heading">{{ $announcement->firstPost->subject }}</h2>
                    <div class="article__date">{{ $announcement->firstPost->poster_time->format('D, jS \o\f F, Y') }}</div>

                    <div class="article__body">
                        {!! $announcement->firstPost->body !!}
                    </div>

                    <div class="article__author">
                        Posted by
                        <img src="https://minotar.net/helm/{{ $announcement->poster->real_name }}/16" width="16" />
                        <a href="#">{{ $announcement->poster->real_name }}</a>
                    </div>
                </div>
                <div class="article__footer">
                    <div class="stats-container">
                        <div class="stat">
                            <span class="stat__figure">{{ $announcement->num_replies }}</span>
                            <span class="stat__heading">Comments</span>
                        </div>
                        <div class="stat">
                            <span class="stat__figure">{{ $announcement->num_views }}</span>
                            <span class="stat__heading">Post Views</span>
                        </div>
                    </div>
                    <div class="actions">
                        <a class="button button--accent button--large" href="http://projectcitybuild.com/forums/index.php?topic={{ $announcement->id_topic}}">
                            Read Post
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    @endif
    
@endsection