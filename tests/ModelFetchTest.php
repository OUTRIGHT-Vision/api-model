<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use Tests\Fakes\City;

class ModelFetchTest extends TestCase
{
    /** @test */
    public function it_should_get_a_city_from_the_api()
    {
        $city = (new City())->find(1);

        $this->assertEquals('Montevideo', $city->name);
    }
}
