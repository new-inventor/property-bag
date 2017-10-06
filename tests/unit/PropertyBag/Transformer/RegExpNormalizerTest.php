<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Exception\TransformationException;
use NewInventor\PropertyBag\Normalizer\RegExpNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class RegExpNormalizerTest extends \Codeception\Test\Unit
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
        $normalizer = RegExpNormalizer::make('/\d{3}/');
        $this->assertSame('123', $normalizer->normalize('123'));
        $this->assertSame('234', $normalizer->normalize('2345'));
        $this->assertNull($normalizer->normalize(null));
        $normalizer = RegExpNormalizer::make('/\d{3}$/');
        $this->assertSame('345', $normalizer->normalize('2345'));
        $this->expectException(TransformationException::class);
        $normalizer->normalize('sadasdasd45');
    }
    
    public function testNormalizer1()
    {
        $normalizer = RegExpNormalizer::make('/\d{3}$/');
        $this->expectException(TypeException::class);
        $normalizer->normalize(['qwe']);
    }
    
    public function testNormalizer2()
    {
        $normalizer = RegExpNormalizer::make('/\d{3}$/');
        $this->expectException(TypeException::class);
        $normalizer->normalize(new \stdClass());
    }
    
    public function testNormalizer3()
    {
        $normalizer = RegExpNormalizer::make('/\d{3}$/');
        $this->expectException(TypeException::class);
        $normalizer->normalize(123);
    }
    
    public function testNormalizer4()
    {
        $normalizer = RegExpNormalizer::make('/\d{3}$/');
        $this->expectException(TypeException::class);
        $normalizer->normalize(false);
    }
    
    public function testNormalizer5()
    {
        $normalizer = RegExpNormalizer::make('/\d{3}$/');
        $this->expectException(TypeException::class);
        $normalizer->normalize(1.1);
    }
}