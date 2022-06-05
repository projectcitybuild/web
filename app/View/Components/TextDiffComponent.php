<?php

namespace App\View\Components;

use Illuminate\Support\HtmlString;
use Illuminate\View\Component;
use Jfcherng\Diff\DiffHelper;

class TextDiffComponent extends Component
{
    private string $renderer = 'Combined';

    private array $diffOptions = [
        'context' => 1,
        'ignoreCase' => false,
        'ignoreWhitespace' => false,
    ];

    private array $rendererOptions = [
        'detailLevel' => 'word',
        'language' => 'eng',
        'showHeader' => false
    ];

    public function __construct(
        private string $attribute,
        private string $old,
        private string $new,
    ) {}

    public function getDiffHtml()
    {
        return new HtmlString(DiffHelper::calculate(
            $this->old, $this->new,
            $this->renderer,
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
