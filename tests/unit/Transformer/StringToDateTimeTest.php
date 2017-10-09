<?php

class StringToDateTimeTest extends \Codeception\Test\Unit
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
        $transformer = new \NewInventor\DataStructure\Transformer\StringToDateTime();
        $this->assertSame('12.12.2017 00:00:00', $transformer->transform('12.12.2017 00:00:00')->format('d.m.Y H:i:s'));
        $transformer = new \NewInventor\DataStructure\Transformer\StringToDateTime('d.m.Y');
        $this->assertSame('12.12.2017', $transformer->transform('12.12.2017')->format('d.m.Y'));
        $this->expectException(\NewInventor\DataStructure\Exception\TransformationException::class);
        $transformer->transform('12.12.2017 00:00:00');
    }
    
    public function test1()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\StringToCamelCase();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1);
    }
    
    public function test2()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\StringToCamelCase();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1.4);
    }
    
    public function test3()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\StringToCamelCase();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(true);
    }
    
    public function test4()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\StringToCamelCase();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform([]);
    }
    
    public function test5()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\StringToCamelCase();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(new stdClass());
    }
}