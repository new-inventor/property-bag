<?php

class StringToLowerCaseTest extends \Codeception\Test\Unit
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
        $transformer = new \NewInventor\DataStructure\Transformer\StringToLowerCase();
        $this->assertSame('qwe asd  zxc', $transformer->transform('qwe asd  Zxc'));
        $this->assertSame('qwe_ asd_zxc123', $transformer->transform('qwe_ asd_zxc123'));
        $this->assertSame('qweasd_zxc', $transformer->transform('qweAsd_zxc'));
        $this->assertSame('qwe asd_zxc', $transformer->transform('Qwe Asd_zxc'));
    }
    
    public function test1()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\StringToLowerCase();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1);
    }
    
    public function test2()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\StringToLowerCase();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1.4);
    }
    
    public function test3()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\StringToLowerCase();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(true);
    }
    
    public function test4()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\StringToLowerCase();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform([]);
    }
    
    public function test5()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\StringToLowerCase();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(new stdClass());
    }
}