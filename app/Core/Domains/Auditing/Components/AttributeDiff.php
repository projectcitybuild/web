<?php

namespace App\Core\Domains\Auditing\Components;

use App\Core\Domains\Auditing\Changes\Change;
use Illuminate\View\Component;

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
        return view('manage.components.audit.diffs.attribute-diff')->with([
            'attribute' => $this->attribute,
            'changeType' => $this->change::class,
            'change' => $this->change,
            'description' => $this->description,
        ]);
    }
}
