<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GptKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'key',
        'prompt',
    ];
}