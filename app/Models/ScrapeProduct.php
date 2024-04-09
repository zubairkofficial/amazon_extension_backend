<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapeProduct extends Model
{
    use HasFactory;

    protected $casts = [
        'dimension' => 'array',
        'description' => 'array',
        'detailInfo' => 'array',
        'colorVariations' => 'array',
        'brandDetails' => 'array',
        'about_this_item' => 'array',
    ];

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = json_encode($value, true);
    }

    public function getDescriptionAttribute($value)
    {
        return json_decode($value, true);
    }
    public function setBrandDetailsAttribute($value)
    {
        $this->attributes['brandDetails'] = json_encode($value, true);
    }

    public function getBrandDetailsAttribute($value)
    {
        return json_decode($value, true);
    }
    public function setAbout_this_itemAttribute($value)
    {
        $this->attributes['about_this_item'] = json_encode($value, true);
    }

    public function getAbout_this_itemAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setDimensionAttribute($value)
    {
        $this->attributes['dimension'] = json_encode($value, true);
    }

    public function getDimensionAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setColorVariationsAttribute($value)
    {
        $this->attributes['colorVariations'] = json_encode($value, true);
    }

    public function getColorVariationsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setDetailInfoAttribute($value)
    {
        $this->attributes['detailInfo'] = json_encode($value, true);
    }

    public function getDetailInfoAttribute($value)
    {
        return json_decode($value, true);
    }
}
