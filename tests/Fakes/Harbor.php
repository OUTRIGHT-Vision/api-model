<?php
namespace Tests\Fakes;

use OUTRIGHTVision\ApiModel;

class Harbor extends ApiModel
{
    public function boats()
    {
        return $this->hasMany(Boat::class, 'boats');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city');
    }

    public function lastCity()
    {
        return $this->hasOne(City::class, 'city');
    }
}
