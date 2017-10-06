<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Exception\TransformationException;
use NewInventor\PropertyBag\Normalizer\MccNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class MccNormalizerTest extends \Codeception\Test\Unit
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
        $normalizer = new MccNormalizer();
        $this->assertSame(505, $normalizer->normalize(505));
        $this->assertNull($normalizer->normalize(null));
        $this->expectException(TransformationException::class);
        $normalizer->normalize(200);
    }
    
    public function testNormalizer1()
    {
        $normalizer = new MccNormalizer();
        $this->expectException(TypeException::class);
        $normalizer->normalize('asdasdasd');
    }
    
    public function testNormalizer2()
    {
        $normalizer = new MccNormalizer();
        $this->expectException(TypeException::class);
        $normalizer->normalize(['asdasdasd']);
    }
    
    public function testNormalizer3()
    {
        $normalizer = new MccNormalizer();
        $this->expectException(TypeException::class);
        $normalizer->normalize(new MccNormalizer());
    }
    
    public function testNormalizer4()
    {
        $normalizer = new MccNormalizer();
        $this->expectException(TypeException::class);
        $normalizer->normalize(true);
    }
    
    public function testNormalizer5()
    {
        $normalizer = new MccNormalizer();
        $this->expectException(TypeException::class);
        $normalizer->normalize('adsfdfsdf');
    }
    
    public function testNormalizer6()
    {
        $normalizer = new MccNormalizer();
        $this->expectException(TypeException::class);
        $normalizer->normalize(123.33);
    }
}