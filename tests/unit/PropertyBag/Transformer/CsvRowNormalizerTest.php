<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Normalizer\CsvRowNormalizer;
use NewInventor\PropertyBag\Normalizer\IntNormalizer;
use NewInventor\PropertyBag\Normalizer\StringNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;
use TestsPropertyBag\TestStringable;

class CsvRowNormalizerTest extends \Codeception\Test\Unit
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
        $normalizer = CsvRowNormalizer::make();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertSame(',', $normalizer->getSeparator());
        $this->assertSame(['123', '456', '789'], $normalizer->normalize('123,456,789'));
        $this->assertNull($normalizer->normalize(null));
        $this->assertSame([null], $normalizer->normalize([null]));
        $this->assertSame([null, 23, 34234], $normalizer->normalize([null, 23, 34234]));
        /** @var CsvRowNormalizer $normalizer */
        $normalizer = CsvRowNormalizer::make('|');
        $this->assertSame(['123', '456', '789'], $normalizer->normalize('123|456|789'));
        $this->assertSame(['123', '456', '', ''], $normalizer->normalize('123|456||'));
        $stringable = new TestStringable();
        $this->assertSame([null, 23, 'dsfadf', $stringable], $normalizer->normalize([null, 23, 'dsfadf', $stringable]));
        $normalizer = CsvRowNormalizer::make(IntNormalizer::make(), '|');
        $this->assertSame([123, 456, 789, 123], $normalizer->normalize('123|456|789|123.2'));
        $normalizer = CsvRowNormalizer::make(
            IntNormalizer::make(),
            IntNormalizer::make(),
            StringNormalizer::make(),
            '|'
        );
        $this->assertSame([123, 456, '789'], $normalizer->normalize('123|456|789'));
        $this->assertSame([123], $normalizer->normalize('123'));
        $this->assertSame([123], $normalizer->normalize(123.2));
        $this->assertSame([null, 345], $normalizer->normalize([null, '345']));
    }
    
    public function testNormalizer1()
    {
        $normalizer = CsvRowNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(new \stdClass());
    }
}