<?php

namespace Tests;

use OUTRIGHTVision\ApiModel;
use Orchestra\Testbench\TestCase;

class HelpersTest extends TestCase
{
    /** @test */
    public function is_iterable_should_work_on_all_php_versions()
    {
        $this->assertTrue(is_iterable([]));
        $this->assertTrue(is_iterable(collect()));
    }

    /** @test */
    public function get_data_should_return_a_single_parameter()
    {
        $this->assertEquals('bar', get_data(['foo' => 'bar'], 'foo'));
    }

    /** @test */
    public function get_data_should_return_data_parameters_using_arrow()
    {
        $this->assertEquals('bar', get_data([
            'foo' => [
                'data' => [
                    'baz' => 'bar',
                ],
            ],
        ], 'foo->baz'));
    }

    /** @test */
    public function get_data_should_return_data_parameters_using_dot_notation()
    {
        $this->assertEquals('bar', get_data([
            'foo' => [
                'data' => [
                    'baz' => 'bar',
                ],
            ],
        ], 'foo.data.baz'));
    }

    /** @test */
    public function get_data_should_return_default_data_when_parameter_does_not_exists()
    {
        $this->assertEquals('bar', get_data([], 'foo', 'bar'));
    }

    /** @test */
    public function get_data_should_return_default_data_when_parameter_is_empty_on_strings()
    {
        $this->assertEquals('bar', get_data(['foo' => null], 'foo', 'bar', false));
        $this->assertEquals('bar', get_data(['foo' => ''], 'foo', 'bar', false));
        $this->assertEquals('bar', get_data(['foo' => []], 'foo', 'bar', false));
    }
}
