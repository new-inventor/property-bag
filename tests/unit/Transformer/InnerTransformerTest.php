<?php

class InnerTransformerTest extends \Codeception\Test\Unit
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
        $transformer = new \NewInventor\DataStructure\Transformer\InnerTransformer(
            new \NewInventor\DataStructure\Transformer\BoolToMixed('true', 'false'),
            new \NewInventor\DataStructure\Transformer\StringToUpperCase()
        );
        $this->assertSame(['true', 'QWE'], $transformer->transform([true, 'qWe']));
        $this->assertSame(['false', 'QWE'], $transformer->transform([false, 'qWe']));
        $transformer = new \NewInventor\DataStructure\Transformer\InnerTransformer();
        $this->assertSame(['123'], $transformer->transform(['123']));
        $this->expectException(\NewInventor\DataStructure\Exception\TransformationException::class);
        $transformer = new \NewInventor\DataStructure\Transformer\InnerTransformer(
            new \NewInventor\DataStructure\Transformer\BoolToMixed('true', 'false'),
            new \NewInventor\DataStructure\Transformer\StringToUpperCase()
        );
        $transformer->transform(['qwe', 123]);
    }
    
    public function test1()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\InnerTransformer();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1);
    }
    
    public function test2()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\InnerTransformer();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(1.0);
    }
    
    public function test3()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\InnerTransformer();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(true);
    }
    
    public function test4()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\InnerTransformer();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform('qwe');
    }
    
    public function test5()
    {
        $transformer = new \NewInventor\DataStructure\Transformer\InnerTransformer();
        $this->expectException(\NewInventor\TypeChecker\Exception\TypeException::class);
        $transformer->transform(new stdClass());
    }
}