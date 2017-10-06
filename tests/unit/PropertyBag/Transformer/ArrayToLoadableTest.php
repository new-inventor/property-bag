<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Exception\TransformationException;
use NewInventor\PropertyBag\Normalizer\PropertyBagRecursiveNormalizer;
use NewInventor\PropertyBag\Transformer\ArrayToLoadable;
use TestsPropertyBag\TestBag;
use TestsPropertyBag\TestStringable;

class ArrayToLoadableTest extends \Codeception\Test\Unit
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
        $transformer = ArrayToLoadable::make(TestBag::class);
        $this->assertEquals(
            '1234567890',
            (string)$transformer->transform(
                [
                    'prop1' => 1,
                    'prop2' => 123,
                    'prop3' => new TestStringable(),
                    'prop4' => '06.09.2017',
                ]
            )->get('prop3')
        );
        $this->expectException(TransformationException::class);
        $transformer->transform(
            [
                'qwe' => 22,
            ]
        );
    }
    
    public function testNormalizer1()
    {
        $this->expectException(\RuntimeException::class);
        ArrayToLoadable::make('class/not/exists');
    }
}