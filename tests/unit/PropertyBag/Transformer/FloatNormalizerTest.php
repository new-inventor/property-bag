<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Normalizer\FloatNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class FloatNormalizerTest extends \Codeception\Test\Unit
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
        $normalizer = FloatNormalizer::make();
        $this->assertNull($normalizer->normalize(null));
        $this->assertSame(1.0, $normalizer->normalize('1'));
        $this->assertSame(1.1, $normalizer->normalize(1.1));
        $this->assertSame(1.1, $normalizer->normalize('1.1'));
    }
    
    public function testNormalizer1()
    {
        $normalizer = FloatNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize('1.1sdfkhsdkf');
    }
    
    public function testNormalizer2()
    {
        $normalizer = FloatNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(['1.1sdfkhsdkf']);
    }
    
    public function testNormalizer3()
    {
        $normalizer = FloatNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(new FloatNormalizer());
    }
}