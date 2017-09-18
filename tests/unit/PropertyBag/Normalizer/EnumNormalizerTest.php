<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Exception\NormalizeException;
use NewInventor\PropertyBag\Normalizer\EnumNormalizer;
use NewInventor\PropertyBag\Normalizer\IntNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class EnumNormalizerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    /** @var EnumNormalizer */
    private $normalizer;
    
    protected function _before()
    {
        $this->normalizer = EnumNormalizer::make(
            [1, 2, 3],
            IntNormalizer::make()
        );
    }
    
    protected function _after()
    {
    }
    
    // tests
    public function testNormalizer()
    {
        $this->assertSame(1, $this->normalizer->normalize(1));
        $this->assertSame(3, $this->normalizer->normalize('3'));
        $this->assertNull($this->normalizer->normalize(null));
    }
    
    public function testNormalizer1()
    {
        $this->expectException(NormalizeException::class);
        $this->normalizer->normalize('5');
    }
    
    public function testNormalizer2()
    {
        $this->expectException(TypeException::class);
        $this->normalizer->normalize('');
    }
    
    public function testNormalizer3()
    {
        $this->expectException(TypeException::class);
        $this->normalizer->normalize(['5']);
    }
    
    public function testNormalizer4()
    {
        $this->expectException(TypeException::class);
        $this->normalizer->normalize(new EnumNormalizer([1, 2, 3]));
    }
}