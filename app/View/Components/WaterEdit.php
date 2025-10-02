<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\WaterIntake;

class WaterEdit extends Component
{
    public $waterintake;

    /**
     * Create a new component instance.
     */
    public function __construct(WaterIntake $waterintake)
    {
        $this->waterintake = $waterintake;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.water-edit');
    }
}
