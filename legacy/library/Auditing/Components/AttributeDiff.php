<?php

namespace Library\Auditing\Components;

use Illuminate\View\Component;
use Library\Auditing\Changes\Change;

class AttributeDiff extends Component
{
    public function __construct(
        private string $attribute,
        private Change $change,
        private string $description = 'Updated',
    ) {
    }

    public function render()
    {
        return view('library.audit.diffs.attribute-diff')->with([
            'attribute' => $this->attribute,
            'changeType' => $this->change::class,
            'change' => $this->change,
            'description' => $this->description,
        ]);
    }
}
