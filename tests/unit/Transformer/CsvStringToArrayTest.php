<?php

class CsvStringToArrayTest extends \Codeception\Test\Unit
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
        $transformer = new \NewInventor\DataStructure\Transformer\CsvStringToArray();
        $this->assertSame([null], $transformer->transform(''));
        $this->assertSame(['123'], $transformer->transform('123'));
        $this->assertSame(['qwe'], $transformer->transform('qwe'));
        $this->assertSame(['true'], $transformer->transform('true'));
        $this->assertSame(['true', 'asd', '123', 'false'], $transformer->transform('true,"asd",123,false'));
        $this->assertSame(['as"d'], $transformer->transform('"as""d"'));
    }
    
    public function test1()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\CsvStringToArray();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1);
    }
    
    public function test2()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\CsvStringToArray();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1.4);
    }
    
    public function test3()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\CsvStringToArray();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(true);
    }
    
    public function test4()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\CsvStringToArray();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform([]);
    }
    
    public function test5()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\CsvStringToArray();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(new stdClass());
    }
}