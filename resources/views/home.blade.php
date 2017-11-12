@extends('layouts.sidebar-layout')

@section('left')
<div id="showcase"></div>

@if(isset($announcements))
    @foreach($announcements as $announcement)
        <article class="panel news-panel">
            <div class="article-contents">
                <h2>{{ $announcement->firstPost->subject }}</h2>
                <div class="date">{{ $announcement->firstPost->poster_time->format('l, jS \of F, Y') }}</div>

                <div class="text">
                    {!! $announcement->firstPost->body !!}
                </div>

                <div class="poster">
                    Posted by
                    <img src="https://minotar.net/helm/{{ $announcement->poster->real_name }}/16" width="16" />
                    <a href="#">{{ $announcement->poster->real_name }}</a>
                </div>
            </div>
            <div class="article-footer">
                <div class="stats">
                    <div class="stat">
                        <h4>{{ $announcement->num_replies }}</h4>
                        <span>Comments</span>
                    </div>
                    <div class="stat">
                        <h4>{{ $announcement->num_views }}</h4>
                        <span>Post Views</span>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn large orange" href="http://projectcitybuild.com/forums/index.php?topic={{ $announcement->id_topic }}">
                        Read Post
                        <i class="fa fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </article>
    @endforeach
@endif

@endsection

@section('right')
<a class="btn-divided orange">
    <div class="icon"><i class="fa fa-angle-right"></i></div>
    <div class="text">Create an Account</div>
</a>

<div class="panel donate-panel">
    <div class="panel-container">
        <h3>Help Keep Us Online</h3>
        <div class="bar-outer">
            <div class="bar-inner" style="width:{{ $donations['percentage'] }}%"></div>
        </div>
        <div class="bar-markers">
            <span>0</span>
            <span>250</span>
            <span>500</span>
            <span>750</span>
            <span>1000</span>
        </div>
    </div>

    <div class="stats">
        <div>
            <h4>${{ $donations['total'] }}</h4>
            <span>Funds Raised</span>
        </div>
        <div>
            <h4>{{ $donations['remainingDays'] }}</h4>
            <span>Remaining Days</span>
        </div>
    </div>

    <div class="panel-container">
        <a class="btn large gray" href="http://projectcitybuild.com/forums/index.php?topic=4124.0">
            Donate
        </a>
        <small>Donators receive a colored name, a reserved server slot and more!</small>
    </div>
</div>

<a class="btn-divided white" href="http://projectcitybuild.com/forums/index.php?topic=6790.0">
    <div class="icon"><i class="fa fa-fw fa-gift"></i></div>
    <div class="text left">
        Vote For Us
        <small>Vote to receive daily in-game prizes</small>
    </div>
</a>
<a class="btn-divided white" href="https://wiki.projectcitybuild.com/">
    <div class="icon"><i class="fa fa-fw fa-wikipedia-w"></i></div>
    <div class="text left">
        Community Wiki
        <small>History, towns and more</small>
    </div>
</a>

<div class="panel forum-activity">
    <h5>Recent Threads</h5>

    @if(isset($recentActivity))
        @foreach($recentActivity as $post)
        <div class="thread">
            <div class="poster">
                {{$post->poster->real_name}} posted in
            </div>
            <a href="http://projectcitybuild.com/forums/index.php?topic={{$post->id_topic}}">{{$post->topic->firstPost->subject}}</a>
            {{$post->poster_time->diffForHumans()}}
        </div>
        @endforeach
    @endif
</div>

<div class="panel discord-panel">
    <iframe src="https://discordapp.com/widget?id=161649330799902720&theme=light" 
        width="100%" 
        height="500" 
        allowtransparency="true" 
        frameborder="0">
    </iframe>
</div>
@endsection