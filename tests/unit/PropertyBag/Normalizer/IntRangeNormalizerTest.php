<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Normalizer\IntRangeNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class IntRangeNormalizerTest extends \Codeception\Test\Unit
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
        $normalizer = IntRangeNormalizer::make();
        $this->assertNull($normalizer->normalize(null));
        $this->assertSame(1, $normalizer->normalize('1'));
        $this->assertSame(1, $normalizer->normalize(1.1));
        $this->assertSame(1, $normalizer->normalize('1.1'));
        
        $normalizer = IntRangeNormalizer::make(1);
        $this->assertSame(3, $normalizer->normalize(3));
        $this->assertSame(1, $normalizer->normalize(1));
        $this->assertSame(1, $normalizer->normalize(0));
        $this->assertSame(1, $normalizer->normalize(-30));
        
        $normalizer = IntRangeNormalizer::make(null, 40);
        $this->assertSame(-3, $normalizer->normalize(-3));
        $this->assertSame(40, $normalizer->normalize(40));
        $this->assertSame(40, $normalizer->normalize(41));
        
        $normalizer = IntRangeNormalizer::make(4, 10);
        $this->assertSame(4, $normalizer->normalize(0));
        $this->assertSame(10, $normalizer->normalize(10));
        $this->assertSame(10, $normalizer->normalize(41));
    }
    
    public function testNormalizer1()
    {
        $normalizer = IntRangeNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize('1.1qwe');
    }
    
    public function testNormalizer2()
    {
        $normalizer = IntRangeNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(['qwe']);
    }
    
    public function testNormalizer3()
    {
        $normalizer = IntRangeNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(new IntRangeNormalizer());
    }
}