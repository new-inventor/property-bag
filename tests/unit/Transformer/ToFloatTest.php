<?php

class ToFloatTest extends \Codeception\Test\Unit
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
        $transformer = new \NewInventor\DataStructure\Transformer\ToFloat();
        $this->assertNull($transformer->transform(null));
        $this->assertSame(123.0, $transformer->transform(123));
        $this->assertSame(123.0, $transformer->transform(123.0));
        $this->assertSame(123.0, $transformer->transform('123.0'));
        $this->assertSame(123.0, $transformer->transform('123'));
    }
    
    public function test2()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\ToFloat();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform('qwe');
    }
    
    public function test3()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\ToFloat();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(true);
    }
    
    public function test4()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\ToFloat();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform([]);
    }
    
    public function test5()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\ToFloat();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(new stdClass());
    }
}