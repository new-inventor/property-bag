<?php

namespace PropertyBag;


use Codeception\Test\Unit;
use NewInventor\PropertyBag\Formatter\BoolFormatter;
use NewInventor\PropertyBag\Normalizer\StringNormalizer;
use NewInventor\PropertyBag\Property;

class PropertyTest extends Unit
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
    
    public function testConstructor()
    {
        $property = new Property();
        $this->assertNull($property->getValue());
        $property = new Property(1);
        $this->assertSame(1, $property->getValue());
    }
    
    public function testGetterSetter()
    {
        $property = new Property();
        $property->setValue('qwe');
        $this->assertSame('qwe', $property->getValue());
    }
    
    public function testNormalizer()
    {
        $property = new Property();
        $property->setNormalizer(StringNormalizer::make());
        $property->setValue(1);
        $this->assertSame('1', $property->getValue());
        $property->setValue(1.1);
        $this->assertSame('1.1', $property->getValue());
        $property->setValue(false);
        $this->assertSame('', $property->getValue());
        $property->setValue(true);
        $this->assertSame('1', $property->getValue());
        $property->setValue('qwe');
        $this->assertSame('qwe', $property->getValue());
    }
    
    public function testFormatter()
    {
        $property = new Property();
        $property->setFormatter(BoolFormatter::make());
        $property->setValue(true);
        $this->assertSame('1', $property->getFormattedValue());
        $property->setValue(false);
        $this->assertSame('0', $property->getFormattedValue());
    }
}