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

    /** @test */
    public function it_should_retain_data_when_doing_copy_constructor()
    {
        $model = new ApiModel(new ApiModel(['foo' => 'bar']));
        $this->assertEquals('bar', $model->foo);
        $this->assertEquals(['foo'=>'bar'], $model->getData());
    }

    /** @test */
    public function it_should_be_serializable_and_unserializable()
    {
        $model = unserialize(serialize(new ApiModel(['foo' => 'bar'])));
        $this->assertEquals('bar', $model->foo);
        $this->assertEquals(['foo'=>'bar'], $model->getData());
    }

    /** @test */
    public function it_should_be_accessed_as_an_array()
    {
        $model =new ApiModel(['foo' => 'bar']);
        $this->assertEquals('bar', $model['foo']);
        $this->assertTrue(isset($model['foo']));
        $model['foo'] = 'baz';

        $this->assertEquals('baz', $model->foo);

        unset($model['foo']);

        $this->assertNull($model->foo);
        $this->assertFalse($model->has('foo'));
    }
}
