<?php

namespace App\View\Components;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Jfcherng\Diff\DiffHelper;

class TextDiffComponent extends Component
{
    private array $diffOptions = [
        'context' => 1,
        'ignoreCase' => false,
        'ignoreWhitespace' => false,
    ];

    private array $rendererOptions = [
        'detailLevel' => 'line',
        'showHeader' => false,
        'lineNumbers' => false
    ];

    public function __construct(
        private string $attribute,
        private string $old,
        private string $new,
    ) {}

    private function getRenderer()
    {
        if (Str::contains($this->new, "\n")) {
            return 'Combined';
        } else {
            return 'SideBySide';
        }
    }

    public function getDiffHtml()
    {
        return new HtmlString(DiffHelper::calculate(
            $this->old, $this->new,
            $this->getRenderer(),
            $this->diffOptions,
            $this->rendererOptions
        ));
    }

    public function render()
    {
        return view('admin.activity.components.diff')->with([
            'attribute' => $this->attribute,
            'diff' => $this->getDiffHtml(),
            'old' => $this->old,
            'new' => $this->new
        ]);
    }
}
