<section class="server-feed">
    @foreach($serverCategories as $category)
    <div class="category">
        <h5 class="category__heading">{{ $category->name }}</h5>
        @foreach($category->servers as $server)
        <div class="server {{ $server->isOnline() ? 'server--online' : 'server--offline' }}">
            <div class="server__title">{{ $server->name }}</div>
            <div class="server__players badge {{ $server->isOnline() ? 'badge--secondary' : 'badge--light' }}">{{ $server->isOnline() ? $server->status->num_of_players.'/'.$server->status->num_of_slots : 'Offline' }}</div>
            <div class="server__ip">{{ $server->ip_alias ?: $server->getAddress() }}</div>
        </div>
        @endforeach
    </div>
    @endforeach
</section>