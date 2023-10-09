<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Checkbox extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        protected string $id,
        protected string $label,
        protected string $value,
        protected string $name,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.checkbox',[
            'id' => $this->id,
            'label' => $this->label,
            'value' => $this->value,
            'name' => $this->name,
        ]);
    }
}
