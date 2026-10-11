<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StatsCard extends Component
{
    public $title;
    public $value;
    public $icon;
    public $color;
    public $trend;
    public $trendValue;

    public function __construct(
        string $title,
        $value,
        string $icon = 'fa-chart-bar',
        string $color = 'primary',
        ?string $trend = null,
        ?string $trendValue = null
    ) {
        $this->title = $title;
        $this->value = $value;
        $this->icon = $icon;
        $this->color = $color;
        $this->trend = $trend;
        $this->trendValue = $trendValue;
    }

    public function render()
    {
        return view('components.stats-card');
    }
}
