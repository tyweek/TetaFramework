<?php

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testTrue()
    {
        $this->assertTrue(true);
    }

    public function testSum()
    {
        $result = 1 + 1;
        $this->assertEquals(2, $result);
    }
}
