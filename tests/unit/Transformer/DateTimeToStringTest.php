<?php

class DateTimeToStringTest extends \Codeception\Test\Unit
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
        $transformer = new \NewInventor\DataStructure\Transformer\DateTimeToString();
        $this->assertSame(
            '12.12.2017 00:00:00',
            $transformer->transform(\DateTime::createFromFormat('d.m.Y H:i:s', '12.12.2017 00:00:00'))
        );
        $transformer = new \NewInventor\DataStructure\Transformer\DateTimeToString('d.m.Y');
        $this->assertSame(
            '12.12.2017',
            $transformer->transform(\DateTime::createFromFormat('d.m.Y H:i:s', '12.12.2017 00:00:00'))
        );
    }
    
    public function test1()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\DateTimeToString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1);
    }
    
    public function test2()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\DateTimeToString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1.4);
    }
    
    public function test3()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\DateTimeToString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform('66');
    }
    
    public function test4()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\DateTimeToString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(true);
    }
    
    public function test5()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\DateTimeToString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform([]);
    }
    
    public function test6()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\DateTimeToString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(new stdClass());
    }
}