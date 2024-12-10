<?php

namespace App\Core\Domains\Auditing\Components;

use App\Core\Domains\Auditing\Changes\Tokens\NotInAudit;
use Illuminate\View\Component;

class InlineDiff extends Component
{
    /**
     * @param  string  $attribute the name of the attribute
     * @param  mixed  $old the old value object
     * @param  mixed  $new the new value object
     * @param  bool  $plain should the diff be rendered without coloured sides
     * @param  string  $description
     */
    public function __construct(
        private string $attribute,
        private mixed $old,
        private mixed $new,
        private bool $plain = false,
        private string $description = 'Updated',
    ) {
    }

    private function oldIsNotInAudit(): bool
    {
        return $this->old instanceof NotInAudit;
    }

    public function render()
    {
        return view('manage.components.audit.diffs.inline-diff')->with([
            'attribute' => $this->attribute,
            'old' => $this->old,
            'new' => $this->new,
            'plain' => $this->plain,
            'oldIsNotInAudit' => $this->oldIsNotInAudit(),
            'description' => $this->description,
        ]);
    }
}
