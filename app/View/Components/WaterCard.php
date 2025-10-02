<?php

namespace App\View\Components;

use Illuminate\View\Component;

class WaterCard extends Component
{
    public $amount;
    public $date;

    /**
     * Constructeur du composant
     */
    public function __construct($amount, $date)
    {
        $this->amount = $amount;
        $this->date = $date;
    }

    /**
     * Rend la vue associée au composant
     */
    public function render()
    {
        return view('components.water-card');
    }
}
