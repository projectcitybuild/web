<ul class="list-group">
    @forelse($entries as $entry)
        <li @class([
            'list-group-item',
            'list-group-item-success' => $entry->getStatus()->isAdded(),
            'list-group-item-danger text-decoration-line-through' => $entry->getStatus()->isRemoved(),
        ])>
            <i @class([
                'me-1 fa-fw',
                'd-inline-block' => $entry->getStatus()->isKept(),
                'fas fa-plus' => $entry->getStatus()->isAdded(),
                'fas fa-minus' => $entry->getStatus()->isRemoved()
            ])></i>{{ $entry->unwrap() }}
        </li>
    @empty
        <li class="list-group-item disabled text-center">
            Empty
        </li>
    @endforelse
</ul>
