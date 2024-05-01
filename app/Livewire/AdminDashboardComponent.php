<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Log;

class AdminDashboardComponent extends Component
{
    use WithPagination;
    public $search;
    public function render()
    {
        $logs = Log::where('asin', 'like', '%' . $this->search . '%')
        ->orWhere('prompt', 'like', '%' . $this->search . '%')
        ->orWhere('summary', 'like', '%' . $this->search . '%')
        ->orWhereHas('user', function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
        ->orderBy('id', 'DESC')
        ->paginate(10);
    
        return view('livewire.admin-dashboard-component',get_defined_vars());
    }
}