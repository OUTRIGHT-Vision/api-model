<?php

namespace Tests\Fakes;

use OUTRIGHTVision\ApiModel;

class Boat extends ApiModel
{
    protected $included_default = ['visitedCities'];

    public function visitedCities()
    {
        return $this->hasMany(City::class, 'visited_cities');
    }
}
