<?php

class BoolToMixedTest extends \Codeception\Test\Unit
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
        $transformer = new \NewInventor\DataStructure\Transformer\BoolToMixed();
        $this->assertSame('1', $transformer->transform(true));
        $this->assertSame('0', $transformer->transform(false));
        $transformer = new \NewInventor\DataStructure\Transformer\BoolToMixed('true', 'false');
        $this->assertSame('true', $transformer->transform(true));
        $this->assertSame('false', $transformer->transform(false));
        $transformer = new \NewInventor\DataStructure\Transformer\BoolToMixed(1, 0);
        $this->assertSame(1, $transformer->transform(true));
        $this->assertSame(0, $transformer->transform(false));
    }
    
    public function test1()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\BoolToMixed();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1);
    }
    
    public function test2()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\BoolToMixed();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1.4);
    }
    
    public function test3()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\BoolToMixed();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform('66');
    }
    
    public function test4()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\BoolToMixed();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform([]);
    }
    
    public function test5()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\BoolToMixed();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(new stdClass());
    }
}