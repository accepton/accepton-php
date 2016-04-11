<?php

namespace AcceptOn;

use AcceptOn\Base;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    protected static $model;

    public function setUp()
    {
        self::$model = new \AcceptOn\Base(array("foo" => "bar", "snake_case" => 1));
    }

    public function testKnownProperty()
    {
        $this->assertEquals("bar", self::$model->foo);
        $this->assertEquals(1, self::$model->snakeCase);
    }
}
