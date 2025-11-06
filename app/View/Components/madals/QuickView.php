<?php

namespace App\View\Components\madals;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class QuickView extends Component
{
    /**
     * Create a new component instance.
     */

    public $title='';
    public $keyId='';

    public function __construct($title, $keyId )
    {
        $this->title = $title;
        $this->keyId = $keyId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.madals.quick-view');
    }
}
