<?php

class ToArrayTest extends \Codeception\Test\Unit
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
        $transformer = new \NewInventor\DataStructure\Transformer\ToArray();
        $this->assertNull($transformer->transform(null));
        $this->assertSame([], $transformer->transform([]));
        $this->assertSame([null], $transformer->transform([null]));
        $this->assertSame(['qwe'], $transformer->transform('qwe'));
        $this->assertSame([123], $transformer->transform(123));
        $this->assertSame([123.0], $transformer->transform(123.0));
        $this->assertSame([true], $transformer->transform(true));
        $class = new stdClass();
        $this->assertSame([$class], $transformer->transform($class));
        $arrayAccess = new \TestsDataStructure\TestIteratorArrayAccess();
        $this->assertSame([], $transformer->transform($arrayAccess));
    }
}