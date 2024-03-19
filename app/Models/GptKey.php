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
        'log_delete_days',
        'is_image_compared',
        'image_model'
    ];
}
