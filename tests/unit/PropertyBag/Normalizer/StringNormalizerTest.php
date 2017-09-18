<?php

namespace PropertyBag\Normalizer;


use TestsPropertyBag\TestStringable;
use NewInventor\PropertyBag\Normalizer\StringNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class StringNormalizerTest extends \Codeception\Test\Unit
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
    public function testNormalizer()
    {
        $normalizer = StringNormalizer::make();
        $this->assertSame('1', $normalizer->normalize(1));
        $this->assertNull($normalizer->normalize(null));
        $this->assertSame('1234567890', $normalizer->normalize(new TestStringable()));
    }
    
    public function testNormalizer1()
    {
        $normalizer = StringNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(new StringNormalizer());
    }
    
    public function testNormalizer2()
    {
        $normalizer = StringNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(['11', '111']);
    }
}