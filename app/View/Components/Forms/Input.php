<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        protected string $type,
        protected string $name,
        protected string $id,
        protected string $label,
        protected string $placeholder = '',
        protected ?string $value = null,
        protected array $additionalClasses = [],
        protected bool $disabled = false,
        protected bool $readonly = false,

    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.input', [
            'type' => $this->type,
            'id' => $this->id,
            'label' => $this->label,
            'placeholder' => $this->placeholder,
            'value' => $this->value,
            'name' => $this->name,
            'classes' => array_merge(
                [
                    "form-control" => !$this->readonly,
                    "form-control-plaintext" => $this->readonly
                ],
                $this->additionalClasses
            ),
            'readonly' => $this->readonly,
        ]);
    }
}
