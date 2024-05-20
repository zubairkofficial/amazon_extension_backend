<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalModel extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'max_tokens',
        'top_p',
        'temp',
        'seed',
        'mode',
        'instruction_template',
        'character',
        'baseUrl',
        'prompt',
    ];
    
}
