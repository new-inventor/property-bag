<?php

class ToStringTest extends \Codeception\Test\Unit
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
        $transformer = new \NewInventor\DataStructure\Transformer\ToString();
        $this->assertNull($transformer->transform(null));
        $this->assertSame('1', $transformer->transform(1));
        $this->assertSame('1.1', $transformer->transform(1.1));
        $this->assertSame('qwe', $transformer->transform('qwe'));
        $this->assertSame('1', $transformer->transform(true));
        $this->assertSame('', $transformer->transform(false));
        $class = new \TestsDataStructure\TestStringable();
        $this->assertSame('1234567890', $transformer->transform($class));
    }
    
    public function test4()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\ToString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform([]);
    }
    
    public function test5()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\ToString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(new stdClass());
    }
}