<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenAIModel extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'value',
        'temp',
        'openai_prompt',
        'json'
    ];

    protected $casts = [
        'json' => 'array',
    ];

    public function setJsonAttribute($value)
    {
        $this->attributes['json'] = json_encode($value, true);
    }

    public function getJsonAttribute($value)
    {
        return json_decode($value, true);
    }
}