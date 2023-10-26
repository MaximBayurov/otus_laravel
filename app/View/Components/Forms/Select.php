<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        protected string $name,
        protected string $id,
        protected string $label,
        protected array $options,
        protected ?string $value = null,
        protected array $additionalClasses = [],
        protected bool $disabled = false,
        protected string $unselectedOption = 'Не выбрано',
        protected ?string $error = null,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.select', [
            'id' => $this->id,
            'label' => $this->label,
            'value' => $this->value,
            'options' => $this->options,
            'name' => $this->name,
            'classes' => array_merge(
                [
                    "form-select",
                    "is-invalid" => !empty($this->error),
                ],
                $this->additionalClasses
            ),
            'disabled' => $this->disabled,
            'unselectedOption' => $this->unselectedOption,
            'error' => $this->error,
        ]);
    }
}
