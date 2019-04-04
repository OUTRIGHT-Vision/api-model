<?php

use OUTRIGHTVision\ApiModel;
use OUTRIGHTVision\Exceptions\ImmutableAttributeException;
use Orchestra\Testbench\TestCase;

class ModelTest extends TestCase
{

    /** @test */
    public function it_should_return_null_for_unexisting_parameters()
    {
        $model = new ApiModel();
        $this->assertNull($model->unexistingParameterXXX);
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
        $model = new class extends ApiModel{
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
}
