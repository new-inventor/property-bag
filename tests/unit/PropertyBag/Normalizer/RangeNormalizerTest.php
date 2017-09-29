<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Normalizer\RangeNormalizer;

class RangeNormalizerTest extends \Codeception\Test\Unit
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
        $normalizer = RangeNormalizer::make(
            1,
            3
        );
        $this->assertSame(1, $normalizer->normalize(0));
        $this->assertSame(3, $normalizer->normalize(15));
        $this->assertSame(3, $normalizer->normalize(3));
        $this->assertSame(1, $normalizer->normalize(1));
        $normalizer = RangeNormalizer::make(
            1,
            3
        );
        $this->assertSame(1, $normalizer->normalize(0));
        $this->assertSame(3, $normalizer->normalize(15));
        $this->assertSame(3, $normalizer->normalize(3));
        $this->assertSame(1, $normalizer->normalize(1));
    }
}