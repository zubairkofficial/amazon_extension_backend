<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WrongPromptResponse as ResponseDataModel;


class WrongPromptResponse extends Component
{   
    use WithPagination;
    public $search;
    public function render()
    {
        $alldata = ResponseDataModel::where('asin', 'like', '%' . $this->search . '%')
        ->orWhere('product_id', 'like', '%' . $this->search . '%')
        ->orWhereHas('log', function ($query) {
            $query->where('id', 'like', '%' . $this->search . '%');
        })
        ->orWhereHas('user', function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
        ->orderBy('id', 'DESC')
        ->paginate(10);
    
        return view('livewire.wrong-prompt-response',get_defined_vars());
    }
}
