<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapeProduct extends Model
{
    use HasFactory;

    protected $casts = [
        'imageUrls' => 'array',
        'dimension' => 'array',
        'detailInfo' => 'array',
        'colorVariations' => 'array',
    ];
    public function setImageUrlsAttribute($value)
    {
        $this->attributes['imageUrls'] = json_encode($value);
    }

    public function getImageUrlsAttribute($value)
    {
        return json_decode($value, true);
    }
    public function setDimensionAttribute($value)
    {
        $this->attributes['dimension'] = json_encode($value);
    }

    public function getDimensionAttribute($value)
    {
        return json_decode($value, true);
    }
    public function setColorVariationsAttribute($value)
    {
        $this->attributes['colorVariations'] = json_encode($value);
    }

    public function getColorariationsVAttribute($value)
    {
        return json_decode($value, true);
    }
    public function setDetailInfoAttribute($value)
    {
        $this->attributes['detailInfo'] = json_encode($value);
    }

    public function getDetailInfoVAttribute($value)
    {
        return json_decode($value, true);
    }
}