<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Exception\NormalizeException;
use NewInventor\PropertyBag\Normalizer\PhoneNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class PhoneNormalizerTest extends \Codeception\Test\Unit
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
        $normalizer = new PhoneNormalizer();
        $this->assertSame('+09876543211', $normalizer->normalize('09876543211'));
        $this->assertSame('+09876543211', $normalizer->normalize('0(987)6543211'));
        $this->assertSame('+09876543211', $normalizer->normalize('+0(987)-654-32-11'));
        $this->assertSame('+09876543211', $normalizer->normalize('+0987-654-3211'));
        $this->assertSame('+09876543211', $normalizer->normalize('+0-9-8-7-6-5-4-3-2-1-1'));
        $this->assertSame('+09876543211', $normalizer->normalize('+09876543211'));
        $this->assertSame('+92300000000', $normalizer->normalize(92300000000));
        $this->expectException(NormalizeException::class);
        $normalizer->normalize('+28739123');
    }
    
    public function testNormalizer0()
    {
        $normalizer = PhoneNormalizer::make();
        $this->expectException(NormalizeException::class);
        $normalizer->normalize('+0-(9-8-7)-6-5-4-3-2-1-1');
    }
    
    public function testNormalizer1()
    {
        $normalizer = PhoneNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(['qwe']);
    }
    
    public function testNormalizer2()
    {
        $normalizer = PhoneNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(new PhoneNormalizer());
    }
    
    public function testNormalizer3()
    {
        $normalizer = PhoneNormalizer::make();
        $this->expectException(TypeException::class);
        $normalizer->normalize(false);
    }
    
    public function testNormalizer4()
    {
        $normalizer = PhoneNormalizer::make();
        $this->expectException(NormalizeException::class);
        $normalizer->normalize(1.1);
    }
}