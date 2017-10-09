<?php

class AsEmptyTest extends \Codeception\Test\Unit
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
        $transformer = new \NewInventor\DataStructure\Transformer\AsEmpty();
        $this->assertNull($transformer->transform(null));
        $this->assertNull($transformer->transform([]));
        $this->assertNull($transformer->transform(''));
        $this->assertNull($transformer->transform(0));
        $this->assertNull($transformer->transform('0'));
        $this->assertNull($transformer->transform(0.0));
        $this->assertNull($transformer->transform(false));
        $this->assertSame('1', $transformer->transform('1'));
        $this->assertSame(1, $transformer->transform(1));
        $this->assertTrue($transformer->transform(true));
        $this->assertSame(1.5, $transformer->transform(1.5));
        $this->assertSame(['1'], $transformer->transform(['1']));
    }
}