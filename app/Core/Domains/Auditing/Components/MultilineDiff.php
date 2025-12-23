<?php

namespace App\Core\Domains\Auditing\Components;

use Illuminate\Support\HtmlString;
use Illuminate\View\Component;
use Jfcherng\Diff\DiffHelper;

class MultilineDiff extends Component
{
    private array $diffOptions = [
        'context' => 1,
        'ignoreCase' => false,
        'ignoreWhitespace' => false,
    ];
    private array $rendererOptions = [
        'showHeader' => false,
        'lineNumbers' => false,
        'separateBlock' => true,
    ];

    public function __construct(
        private string $attribute,
        private string $old,
        private string $new,
        private string $description = 'Updated'
    ) {}

    private function getDiffHtml()
    {
        return new HtmlString(DiffHelper::calculate(
            $this->old, $this->new,
            'Combined',
            $this->diffOptions,
            $this->rendererOptions
        ));
    }

    public function render()
    {
        return view('manage.components.audit.diffs.multiline-diff')->with([
            'attribute' => $this->attribute,
            'diff' => $this->getDiffHtml(),
            'old' => $this->old,
            'new' => $this->new,
            'description' => $this->description,
        ]);
    }
}
