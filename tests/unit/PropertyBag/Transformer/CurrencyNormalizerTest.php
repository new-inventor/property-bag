<?php

namespace PropertyBag\Normalizer;


use Codeception\Test\Unit;
use NewInventor\PropertyBag\Exception\TransformationException;
use NewInventor\PropertyBag\Normalizer\CurrencyNormalizer;

class CurrencyNormalizerTest extends Unit
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
    public function testSomeFeature()
    {
        $normalizer = new CurrencyNormalizer();
        $this->assertSame('RUR', $normalizer->normalize('rur'));
        $this->assertSame('EUR', $normalizer->normalize('EUR'));
        $this->assertNull($normalizer->normalize(null));
        $this->expectException(TransformationException::class);
        $normalizer->normalize('YYY');
    }
}