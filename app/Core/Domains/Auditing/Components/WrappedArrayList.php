<?php

namespace App\Core\Domains\Auditing\Components;

use App\Core\Domains\Auditing\Changes\ArrayDiff\ArrayWrapState;
use App\Core\Domains\Auditing\Changes\ArrayDiff\WrappedArrayEntry;
use Illuminate\View\Component;

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
        return view('manage.components.audit.diffs.wrapped-array-list')->with([
            'entries' => $this->entries,
        ]);
    }
}
