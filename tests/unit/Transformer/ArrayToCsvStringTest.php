<?php

class ArrayToCsvStringTest extends \Codeception\Test\Unit
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
        $data = [123, 'asdasd', true, '123123', 123];
        $transformer = new \NewInventor\DataStructure\Transformer\ArrayToCsvString();
        $this->assertSame('123,asdasd,1,123123,123', $transformer->transform($data));
        $this->assertNull($transformer->transform(null));
        $transformer = new \NewInventor\DataStructure\Transformer\ArrayToCsvString(',', '"', '\\', true);
        $this->assertSame('123,"asdasd",1,"123123",123', $transformer->transform($data));
        $transformer = new \NewInventor\DataStructure\Transformer\ArrayToCsvString('|', '_', '+');
        $this->assertSame('123|asdasd|1|123123|123', $transformer->transform($data));
        $data = ['123+123', 'qwe_asd', 'zxc|zxc'];
        $this->assertSame('_123+123_|_qwe__asd_|_zxc|zxc_', $transformer->transform($data));
    }
    
    public function test1()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\ArrayToCsvString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1);
    }
    
    public function test2()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\ArrayToCsvString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1.0);
    }
    
    public function test3()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\ArrayToCsvString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(true);
    }
    
    public function test4()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\ArrayToCsvString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform('qwe');
    }
    
    public function test5()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\ArrayToCsvString();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(new stdClass());
    }
}