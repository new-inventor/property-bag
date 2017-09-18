<?php

namespace PropertyBag\Normalizer;


use TestsPropertyBag\TestBag;
use TestsPropertyBag\TestStringable;
use NewInventor\PropertyBag\Exception\PropertyNotFoundException;
use NewInventor\PropertyBag\Normalizer\PropertyBagNormalizer;
use NewInventor\PropertyBag\PropertyBag;

class PropertyBagNormalizerTest extends \Codeception\Test\Unit
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
        PropertyBag::setCacheDriver(null);
        $normalizer = PropertyBagNormalizer::make(TestBag::class);
        $this->assertEquals(
            '1234567890',
            (string)$normalizer->normalize(
                [
                    'prop1' => 1,
                    'prop2' => 123,
                    'prop3' => new TestStringable(),
                    'prop4' => '06.09.2017',
                ]
            )->get('prop3')
        );
        $this->expectException(PropertyNotFoundException::class);
        $normalizer->normalize(
            [
                'qwe' => 22,
            ]
        );
    }
    
    public function testNormalizer1()
    {
        PropertyBag::setCacheDriver(null);
        $this->expectException(\RuntimeException::class);
        PropertyBagNormalizer::make('class/not/exists');
    }
}