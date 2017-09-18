<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Normalizer\ArrayNormalizer;
use NewInventor\PropertyBag\Normalizer\EnumNormalizer;
use NewInventor\PropertyBag\Normalizer\IntNormalizer;
use NewInventor\PropertyBag\Normalizer\StringNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class ArrayNormalizerTest extends \Codeception\Test\Unit
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
        $normalizer = ArrayNormalizer::make();
        $this->assertSame(['asd', 'qwe'], $normalizer->normalize(['asd', 'qwe']));
        $this->assertSame(['3'], $normalizer->normalize('3'));
        $this->assertSame([3], $normalizer->normalize(3));
        $this->assertNull($normalizer->normalize(null));
        $this->assertSame([null], $normalizer->normalize([null]));
        $this->assertSame([false], $normalizer->normalize(false));
        $this->assertSame([false], $normalizer->normalize(false));
        $normalizer = ArrayNormalizer::make(
            StringNormalizer::make()
        );
        $this->assertSame(['1', '0.4', 'sdfsdf', ''], $normalizer->normalize([1, 0.4, 'sdfsdf', false]));
        
        $normalizer = ArrayNormalizer::make(
            StringNormalizer::make(),
            EnumNormalizer::make(
                [1, 2, 3],
                IntNormalizer::make()
            )
        );
        $this->assertSame(['5', 3], $normalizer->normalize([5, 3]));
        $this->assertSame(['3.1', 2], $normalizer->normalize(['3.1', '2']));
        $this->expectException(\InvalidArgumentException::class);
        $normalizer->normalize(['3.1', 4]);
    }
    
    public function testNormalizer1()
    {
        $normalizer = ArrayNormalizer::make(
            IntNormalizer::make(),
            EnumNormalizer::make(
                [1, 2, 3],
                IntNormalizer::make()
            )
        );
        $this->expectException(TypeException::class);
        $normalizer->normalize(['3.1', 'asdfsfd']);
    }
    
    public function testNormalizer2()
    {
        $normalizer = ArrayNormalizer::make(
            IntNormalizer::make(),
            EnumNormalizer::make(
                [1, 2, 3],
                IntNormalizer::make()
            )
        );
        $this->expectException(TypeException::class);
        $normalizer->normalize(['fsdf', '3']);
    }
}