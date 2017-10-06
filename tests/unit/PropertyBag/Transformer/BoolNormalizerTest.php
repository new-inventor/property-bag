<?php

namespace PropertyBag\Normalizer;


use Codeception\Test\Unit;
use NewInventor\PropertyBag\Normalizer\BoolNormalizer;

class BoolNormalizerTest extends Unit
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
        $normalizer = BoolNormalizer::make();
        $this->assertTrue($normalizer->normalize(1));
        $this->assertTrue($normalizer->normalize('qwe'));
        $this->assertFalse($normalizer->normalize(false));
        $this->assertFalse($normalizer->normalize(''));
        $this->assertNull($normalizer->normalize(null));
        $this->assertFalse($normalizer->normalize([]));
        $this->assertFalse($normalizer->normalize(0));
        $this->assertFalse($normalizer->normalize(0.0));
        $this->assertFalse($normalizer->normalize('0'));
        $normalizer = BoolNormalizer::make(
            ['true', 'yes'],
            ['false', 'no']
        );
        $this->assertTrue($normalizer->normalize(1));
        $this->assertTrue($normalizer->normalize('true'));
        $this->assertFalse($normalizer->normalize('false'));
        $this->assertTrue($normalizer->normalize('yes'));
        $this->assertFalse($normalizer->normalize('no'));
        $this->assertFalse($normalizer->normalize('0'));
    }
}