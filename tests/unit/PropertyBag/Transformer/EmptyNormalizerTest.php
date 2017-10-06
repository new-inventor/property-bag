<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Normalizer\EmptyNormalizer;

class EmptyNormalizerTest extends \Codeception\Test\Unit
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
        $normalizer = EmptyNormalizer::make();
        $this->assertEquals(1, $normalizer->normalize(1));
        $this->assertEquals('qwe', $normalizer->normalize('qwe'));
        $this->assertNull($normalizer->normalize(''));
        $this->assertNull($normalizer->normalize(null));
        $this->assertNull($normalizer->normalize([]));
        $this->assertNull($normalizer->normalize(0));
        $this->assertNull($normalizer->normalize(0.0));
        $this->assertNull($normalizer->normalize('0'));
        $this->assertNull($normalizer->normalize(false));
    }
}