<?php

use Mockery as M;

class TestCase extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        M::close();
        parent::tearDown();
    }
}
