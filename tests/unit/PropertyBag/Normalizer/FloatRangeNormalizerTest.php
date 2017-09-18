<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Normalizer\FloatRangeNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class FloatRangeNormalizerTest extends \Codeception\Test\Unit
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
        $normalizer = FloatRangeNormalizer::make();
        $this->assertNull(null, $normalizer->normalize(null));
        $this->assertSame(1.0, $normalizer->normalize('1'));
        $this->assertSame(1.0, $normalizer->normalize(1));
        $this->assertSame(1.1, $normalizer->normalize(1.1));
        $this->assertSame(1.1, $normalizer->normalize('1.1'));
        
        $normalizer = FloatRangeNormalizer::make(1);
        $this->assertSame(3.5, $normalizer->normalize(3.5));
        $this->assertSame(1.0, $normalizer->normalize(1.0));
        $this->assertSame(1.0, $normalizer->normalize(0));
        $this->assertSame(1.0, $normalizer->normalize(-30));
        
        $normalizer = FloatRangeNormalizer::make(null, 40.5);
        $this->assertSame(0.0, $normalizer->normalize(0));
        $this->assertSame(40.5, $normalizer->normalize(40.5));
        $this->assertSame(40.5, $normalizer->normalize(41));
        
        $normalizer = FloatRangeNormalizer::make(2, 40);
        $this->assertSame(2.0, $normalizer->normalize(0));
        $this->assertSame(10.0, $normalizer->normalize(10));
        $this->assertSame(40.0, $normalizer->normalize(41));
    }
    
    public function testNormalizer1()
    {
        $normalizer = FloatRangeNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize('1.1qwe');
    }
    
    public function testNormalizer2()
    {
        $normalizer = FloatRangeNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(['qwe']);
    }
    
    public function testNormalizer3()
    {
        $normalizer = FloatRangeNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(new FloatRangeNormalizer());
    }
}