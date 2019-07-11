<?php

namespace Tests\Fakes;

use OUTRIGHTVision\ApiModel;

class Harbor extends ApiModel
{
    protected $cast_model = ['city' => City::class];

    public function boats()
    {
        return $this->hasMany(Boat::class, 'boats');
    }

    public function originCity()
    {
        return $this->belongsTo(City::class, 'city');
    }

    public function lastCity()
    {
        return $this->hasOne(City::class, 'city');
    }
}
