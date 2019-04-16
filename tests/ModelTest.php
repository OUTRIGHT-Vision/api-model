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

class ModelTest extends TestCase
{

    /** @test */
    public function it_should_return_null_for_unexisting_parameters()
    {
        $model = new ApiModel();
        $this->assertNull($model->unexistingParameterXXX);
    }

    /** @test */
    public function it_should_work_with_invalid_values()
    {
        $model = new ApiModel(new \stdClass);
        $this->assertNull($model->unexistingParameterXXX);
        $this->assertFalse($model->has('house'));
        $this->assertEmpty($model->toArray());
    }

    /** @test */
    public function it_should_return_valid_value_for_an_existing_parameter()
    {
        $model = new ApiModel(['foo' => 'bar']);
        $this->assertEquals('bar', $model->foo);
    }

    /** @test */
    public function it_should_assign_a_value_to_a_parameter()
    {
        $model = new ApiModel();
        $this->assertNull($model->foo);
        $model->foo = 'bar';
        $this->assertEquals('bar', $model->foo);
        $this->assertEquals(['foo' => 'bar'], $model->toArray());
    }

    /** @test */
    public function it_should_throw_an_error_on_immutble_parameters()
    {
        $model = new class extends ApiModel
        {
            public function getFooAttribute()
            {
                return 'bar';
            }
        };
        $this->assertEquals('bar', $model->foo);
        $this->assertEquals([], $model->toArray());

        $this->expectException(ImmutableAttributeException::class);
        $model->foo = 'baz';
    }

    /** @test */
    public function it_should_throw_an_error_on_immutble_methods()
    {
        $model = new class extends ApiModel
        {
            public function fakeMethod(): string
            {
                return 'I return a string';
            }
        };
        $this->assertEquals('I return a string', $model->fakeMethod());
        $this->assertEquals([], $model->toArray());

        $this->expectException(ImmutableAttributeException::class);
        $model->fakeMethod = 'baz';
    }

    /** @test */
    public function it_should_return_null_getting_value_on_defined_methods()
    {
        $model = new class extends ApiModel
        {
            public function fakeMethod(): string
            {
                return 'I return a string';
            }
        };
        $this->assertEquals('I return a string', $model->fakeMethod());
        $this->assertNull($model->fakeMethod);
        $this->assertEquals([], $model->toArray());
    }

    /** @test */
    public function it_should_retain_data_when_doing_copy_constructor()
    {
        $model = new ApiModel(new ApiModel(['foo' => 'bar']));
        $this->assertEquals('bar', $model->foo);
        $this->assertEquals(['foo' => 'bar'], $model->getAttributes());
    }

    /** @test */
    public function it_should_be_serializable_and_unserializable()
    {
        $model = unserialize(serialize(new ApiModel(['foo' => 'bar'])));
        $this->assertEquals('bar', $model->foo);
        $this->assertEquals(['foo' => 'bar'], $model->getAttributes());
    }

    /** @test */
    public function it_should_be_accessed_as_an_array()
    {
        $model = new ApiModel(['foo' => 'bar']);
        $this->assertEquals('bar', $model['foo']);
        $this->assertTrue(isset($model['foo']));
        $model['foo'] = 'baz';

        $this->assertEquals('baz', $model->foo);

        unset($model['foo']);

        $this->assertNull($model->foo);
        $this->assertFalse($model->has('foo'));
    }

    /** @test */
    public function it_should_return_false_if_the_model_do_not_have_id()
    {
        $model = new ApiModel(['foo' => 'bar']);

        $this->assertFalse($model->exists());
    }

    /** @test */
    public function it_should_return_true_if_the_model_has_an_id()
    {
        $model = new ApiModel(['id' => 111222]);

        $this->assertTrue($model->exists());
    }

    /** @test */
    public function it_should_cast_dates_to_carbon_instances()
    {
        $model = new ApiModel(['created_at' => '2019-01-01 00:00:00']);

        $this->assertInstanceOf(Carbon::class, $model->created_at);
    }

    /** @test */
    public function it_should_cast_single_relationship_to_model()
    {
        $harbor = new Harbor([
            'city' => [
                'data' => [
                    'id'   => 1,
                    'name' => 'Montevideo',
                ],
            ],
        ]);

        $this->assertInstanceOf(City::class, $harbor->city);
    }

    /** @test */
    public function it_should_cast_single_relationship_to_model_event_without_data()
    {
        $harbor = new Harbor([
            'city' => [
                'id'   => 1,
                'name' => 'Montevideo',
            ],
        ]);

        $this->assertInstanceOf(City::class, $harbor->city);
    }

    /** @test */
    public function it_should_refresh_a_relationship_when_setting_new_value()
    {
        $harbor = new Harbor([
            'city' => [
                'id'   => 1,
                'name' => 'Montevideo',
            ],
        ]);

        $this->assertInstanceOf(City::class, $harbor->city);
        $this->assertEquals('Montevideo', $harbor->city->name);
        $harbor->city = ['name' => 'London'];
        $this->assertInstanceOf(City::class, $harbor->city);
        $this->assertEquals('London', $harbor->city->name);
    }

    /** @test */
    public function it_should_property_from_relationship_should_be_accessed()
    {
        $harbor = new Harbor([
            'city' => [
                'data' => [
                    'id'   => 1,
                    'name' => 'Montevideo',
                ],
            ],
        ]);

        $this->assertEquals('Montevideo', $harbor->city->name);
        $this->assertTrue($harbor->city->exists());
    }    

    /** @test */
    public function it_should_property_from_relationship_should_be_accessed_double()
    {
        $harbor = new Harbor([
            'city' => [
                'data' => [
                    'id'   => 1,
                    'name' => 'Montevideo',
                ],
            ],
        ]);

        $this->assertEquals('Montevideo', $harbor->lastCity->name);
        $this->assertTrue($harbor->lastCity->exists());
    }

    /** @test */
    public function it_should_return_a_collection_of_boats()
    {
        $harbor = new Harbor([
            'boats' => [
                'data' => [
                    [
                        'id'   => 1,
                        'name' => 'Queen Mary',
                    ],
                    [
                        'id'   => 2,
                        'name' => 'Reconcho',
                    ],
                ],
            ],
        ]);
        $this->assertInstanceOf(HasMany::class, $harbor->boats);
        $this->assertTrue($harbor->boats->first()->exists());
    }

    /** @test */
    public function it_should_refresh_a_collection_of_relationships()
    {
        $harbor = new Harbor([
            'boats' => [
                'data' => [
                    [
                        'id'   => 1,
                        'name' => 'Queen Mary',
                    ],
                    [
                        'id'   => 2,
                        'name' => 'Reconcho',
                    ],
                ],
            ],
        ]);
        $this->assertInstanceOf(HasMany::class, $harbor->boats);
        $this->assertTrue($harbor->boats->first()->exists());

        $harbor->boats = [
            'data' => [
                [
                    'name' => 'Rocinante',
                ],
            ],
        ];

        $this->assertInstanceOf(HasMany::class, $harbor->boats);
        $this->assertFalse($harbor->boats->first()->exists());
        $this->assertEquals('Rocinante', $harbor->boats->first()->name);
    }

    /** @test */
    public function it_should_eager_load_default_relationships()
    {
        $boat = new Boat([
            'visited_cities' => [
                'data' => [
                    [
                        'name' => 'Montevideo',
                    ],
                    [
                        'name' => 'London',
                    ],
                ],
            ],
        ]);

        $this->assertInstanceOf(HasMany::class, $boat->visitedCities);
    }

    /** @test */
    public function it_should_not_eager_load_any_relationship_if_not_in_include_default_array()
    {
        $model = new class([
            'visited_cities' => [
                'data' => [
                    [
                        'name' => 'Montevideo',
                    ],
                    [
                        'name' => 'London',
                    ],
                ],
            ],
        ]) extends ApiModel{
            public function visitedCities()
            {
                return $this->hasMany(City::class, 'visited_cities');
            }
        };

        $this->assertInstanceOf(HasMany::class, $model->visitedCities);
        $this->assertEquals('Montevideo', $model->visitedCities->first()->name);
    }

}
