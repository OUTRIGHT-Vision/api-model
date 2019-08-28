<?php

namespace Tests;

use Carbon\Carbon;
use Orchestra\Testbench\TestCase;
use OUTRIGHTVision\ApiModel;
use OUTRIGHTVision\Exceptions\ImmutableAttributeException;
use OUTRIGHTVision\Relationships\HasMany;
use Tests\Fakes\Boat;
use Tests\Fakes\City;
use Tests\Fakes\Harbor;

class ModelFetchTest extends TestCase
{
    /** @test */
    public function it_should_get_a_city_from_the_api()
    {
        $city = (new City)->find(1);

        $this->assertEquals('Montevideo', $city->name);
    }
}
