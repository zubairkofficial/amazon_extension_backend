<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Selector;

class SelectorStatusToggle extends Component
{
    public $selectors;

    protected $listeners = ['toggleStatus'];

    public function mount()
    {
        $this->selectors = Selector::all();
    }

    public function toggleStatus($selectorId)
    {
        $selector = Selector::find($selectorId);
        $selector->status = $selector->status === 'enable' ? 'disable' : 'enable';
        $selector->save();
        $this->selectors = Selector::all(); // Refresh the data
    }

    public function render()
    {
        return view('livewire.selector-status-toggle');
    }
}
