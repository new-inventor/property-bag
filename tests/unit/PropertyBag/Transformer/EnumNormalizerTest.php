<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Exception\TransformationException;
use NewInventor\PropertyBag\Normalizer\EnumNormalizer;
use NewInventor\PropertyBag\Normalizer\IntNormalizer;

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
        $this->normalizer = EnumNormalizer::make([1, 2, 3]);
    }
    
    protected function _after()
    {
    }
    
    // tests
    public function testNormalizer()
    {
        $this->assertSame(1, $this->normalizer->normalize(1));
        $this->assertNull($this->normalizer->normalize(null));
    }
    
    public function testNormalizer1()
    {
        $this->expectException(TransformationException::class);
        $this->normalizer->normalize(5);
    }
    
    public function testNormalizer2()
    {
        $this->expectException(TransformationException::class);
        $this->normalizer->normalize('');
    }
    
    public function testNormalizer3()
    {
        $this->expectException(TransformationException::class);
        $this->normalizer->normalize(['5']);
    }
    
    public function testNormalizer4()
    {
        $this->expectException(TransformationException::class);
        $this->normalizer->normalize(new EnumNormalizer([1, 2, 3]));
    }
}