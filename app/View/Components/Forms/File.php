<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class File extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        protected string $name,
        protected string $id,
        protected string $label,
        protected array $additionalClasses = [],
        protected bool $readonly = false,
        protected ?string $error = null,
        protected bool $multiple = false,
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.file', [
            'id' => $this->id,
            'label' => $this->label,
            'name' => $this->name,
            'classes' => array_merge(
                [
                    "form-control" => true,
                    "is-invalid" => !empty($this->error),
                ],
                $this->additionalClasses
            ),
            'readonly' => $this->readonly,
            'error' => $this->error,
            'multiple' => $this->multiple,
        ]);
    }
}
