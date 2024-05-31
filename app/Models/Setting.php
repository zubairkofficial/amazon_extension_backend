<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'log_delete_days',
        'is_image_compared',
        'model_type',
        'open_ai_model_id',
        'local_model_id',
        'imagecompare_model_id'
    ];

    public function local_model()
    {
        return $this->hasOne(LocalModel::class, 'id', 'local_model_id');
    }
    public function openai_model()
    {
        return $this->hasOne(OpenAIModel::class, 'id', 'open_ai_model_id');
    }
    public function imageCompare_model()
    {
        return $this->hasOne(ImageCompareModel::class, 'id', 'imagecompare_model_id');
    }
}
