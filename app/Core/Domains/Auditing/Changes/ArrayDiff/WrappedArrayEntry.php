<?php

namespace App\Core\Domains\Auditing\Changes\ArrayDiff;

use Illuminate\Support\Collection;

/**
 * Wraps array entries with a state annotation
 *
 * @template T
 */
class WrappedArrayEntry
{
    /**
     * Wrap a collection of array entries in a state
     *
     * @param  Collection<T>  $entries
     * @return Collection<int,WrappedArrayEntry<T>>
     */
    public static function wrapAll(Collection $entries, ArrayWrapState $state): Collection
    {
        return $entries->map(function ($entry) use ($state) {
            return new WrappedArrayEntry($entry, $state);
        });
    }

    /**
     * @param  T  $value
     */
    public function __construct(
        private mixed $value,
        private ArrayWrapState $status
    ) {}

    public function getStatus(): ArrayWrapState
    {
        return $this->status;
    }

    /**
     * Retrieve the original value
     *
     * @return T
     */
    public function unwrap()
    {
        return $this->value;
    }
}
