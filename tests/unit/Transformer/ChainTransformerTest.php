<?php

class ChainTransformerTest extends \Codeception\Test\Unit
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
        $transformer = new \NewInventor\DataStructure\Transformer\ChainTransformer(
            new \NewInventor\DataStructure\Transformer\BoolToMixed('true', 'false'),
            new \NewInventor\DataStructure\Transformer\StringToUpperCase()
        );
        $this->assertSame('FALSE', $transformer->transform(false));
        $this->assertSame('TRUE', $transformer->transform(true));
        $transformer = new \NewInventor\DataStructure\Transformer\ChainTransformer();
        $this->assertSame('qwe', $transformer->transform('qwe'));
    }
}