<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
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
        protected int $height = 200,
        protected ?string $error = null,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.textarea', [
            'type' => $this->type,
            'id' => $this->id,
            'label' => $this->label,
            'placeholder' => $this->placeholder,
            'value' => $this->value,
            'name' => $this->name,
            'classes' => array_merge(
                [
                    "form-control" => !$this->readonly,
                    "form-control-plaintext" => $this->readonly,
                    "is-invalid" => !empty($this->error),
                ],
                $this->additionalClasses
            ),
            'readonly' => $this->readonly,
            'height' => $this->height,
            'error' => $this->error,
        ]);
    }
}
