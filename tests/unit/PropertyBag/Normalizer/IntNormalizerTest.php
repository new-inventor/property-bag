<?php

namespace PropertyBag\Normalizer;


use Codeception\Test\Unit;
use NewInventor\PropertyBag\Normalizer\IntNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class IntNormalizerTest extends Unit
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
        $normalizer = IntNormalizer::make();
        $this->assertNull($normalizer->normalize(null));
        $this->assertSame(1, $normalizer->normalize('1'));
        $this->assertSame(1, $normalizer->normalize(1.1));
        $this->assertSame(1, $normalizer->normalize('1.1'));
    }
    
    public function testNormalizer1()
    {
        $normalizer = IntNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize('1.2jdfhkjsdf');
    }
    
    public function testNormalizer2()
    {
        $normalizer = IntNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(new IntNormalizer());
    }
    
    public function testNormalizer3()
    {
        $normalizer = IntNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(['qwe', 'rty']);
    }
}