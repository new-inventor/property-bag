<?php

namespace PropertyBag\Normalizer;


use Codeception\Test\Unit;
use NewInventor\PropertyBag\Exception\TransformationException;
use NewInventor\PropertyBag\Normalizer\DateTimeNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class DateTimeNormalizerTest extends Unit
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
        $normalizer = DateTimeNormalizer::make('d.m.Y');
        $this->assertSame(
            '06.09.2017',
            $normalizer->normalize(\DateTime::createFromFormat('U', '1504715838')->format('d.m.Y'))->format('d.m.Y')
        );
        $this->assertNull($normalizer->normalize(null));
        $this->expectException(TransformationException::class);
        $normalizer->normalize('24.06.2017 08:09');
    }
    
    public function testNormalizer1()
    {
        $normalizer = DateTimeNormalizer::make('d.m.Y');
        $this->expectException(TransformationException::class);
        $normalizer->normalize('24.06.2017 08:09');
    }
    
    public function testNormalizer2()
    {
        $normalizer = DateTimeNormalizer::make('d.m.Y');
        $this->expectException(TypeException::class);
        $normalizer->normalize(1);
    }
    
    public function testNormalizer3()
    {
        $normalizer = DateTimeNormalizer::make('d.m.Y');
        $this->expectException(TypeException::class);
        $normalizer->normalize(1.1);
    }
    
    public function testNormalizer4()
    {
        $normalizer = DateTimeNormalizer::make('d.m.Y');
        $this->expectException(TypeException::class);
        $normalizer->normalize([1.1]);
    }
    
    public function testNormalizer5()
    {
        $normalizer = DateTimeNormalizer::make('d.m.Y');
        $this->expectException(TypeException::class);
        $normalizer->normalize(new DateTimeNormalizer());
    }
}