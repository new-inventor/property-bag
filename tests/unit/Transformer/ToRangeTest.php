<?php

class ToRangeTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }
    
    protected function _after()
    {
    }
    
    // tests
    public function testSomeFeature()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\ToRange(1, 10);
        $this->assertSame(1, $transformer->transform(1));
        $this->assertSame(2, $transformer->transform(2));
        $this->assertSame(10, $transformer->transform(10));
        $this->assertSame(10, $transformer->transform(11));
        $this->assertSame(1, $transformer->transform(0));
    }
}