<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MyComponent extends Component
{
    public $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function render()
    {
        return view('components.my-component');
    }
}