<?php

namespace PropertyBag\Normalizer;


use NewInventor\PropertyBag\Exception\TransformationException;
use NewInventor\PropertyBag\Normalizer\EmailNormalizer;
use NewInventor\TypeChecker\Exception\TypeException;

class EmailNormalizerTest extends \Codeception\Test\Unit
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
        $normalizer = new EmailNormalizer();
        $this->assertNull($normalizer->normalize(null));
        $this->assertSame('123@qwe.ru', $normalizer->normalize('sdfgsdfgsdfg dsfgsdf 123@qwe.ru fdgsdfgsfg'));
        $this->assertSame('asd.asd@asd.qwe.qwe', $normalizer->normalize('asd.asd@asd.qwe.qwe'));
        $this->assertSame('firstname+lastname@example.com', $normalizer->normalize('firstname+lastname@example.com'));
        $this->assertSame('email@123.123.123.123', $normalizer->normalize('email@123.123.123.123'));
        $this->assertSame("\"\\x0Bfhgjdfg\t\"@example.com", $normalizer->normalize("\"\\x0Bfhgjdfg\t\"@example.com"));
    
        $this->expectException(TransformationException::class);
        $normalizer->normalize('sadasdasd45');
    }
    
    // tests
    public function testNormalizer1()
    {
        $normalizer = new EmailNormalizer();
        $this->expectException(TypeException::class);
        $normalizer->normalize(['sadasdasd45']);
    }
    
    // tests
    public function testNormalizer2()
    {
        $normalizer = new EmailNormalizer();
        $this->expectException(TypeException::class);
        $normalizer->normalize(new \stdClass());
    }
    
    // tests
    public function testNormalizer3()
    {
        $normalizer = new EmailNormalizer();
        $this->expectException(TypeException::class);
        $normalizer->normalize(1);
    }
    
    // tests
    public function testNormalizer4()
    {
        $normalizer = new EmailNormalizer();
        $this->expectException(TypeException::class);
        $normalizer->normalize(1.4);
    }
    
    // tests
    public function testNormalizer5()
    {
        $normalizer = new EmailNormalizer();
        $this->expectException(TypeException::class);
        $normalizer->normalize(false);
    }
}