<?php

namespace Library\Auditing\Components;

use Illuminate\View\Component;
use Library\Auditing\Changes\ArrayDiff\ArrayWrapState;
use Library\Auditing\Changes\ArrayDiff\WrappedArrayEntry;

class WrappedArrayList extends Component
{
    /**
     * @var array<WrappedArrayEntry>
     */
    private array $entries;

    /**
     * Create a new component instance.
     *
     * @param  array<WrappedArrayEntry>  $entries
     * @param  string  $excludeType
     */
    public function __construct(array $entries, string $excludeType)
    {
        $excludeType = ArrayWrapState::tryFrom($excludeType);
        $this->entries = collect($entries)->reject(function (WrappedArrayEntry $e) use ($excludeType) {
            return $e->getStatus() == $excludeType;
        })->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('library.audit.diffs.wrapped-array-list')->with([
            'entries' => $this->entries,
        ]);
    }
}
