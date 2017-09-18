<?php

namespace PropertyBag\Formatter;


use NewInventor\PropertyBag\Formatter\BoolFormatter;
use NewInventor\TypeChecker\Exception\TypeException;

class BoolFormatterTest extends \Codeception\Test\Unit
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
    public function testFormatter()
    {
        $formatter = BoolFormatter::make();
        $this->assertNull($formatter->format(null));
        $this->assertSame('1', $formatter->format(true));
        $this->assertSame('0', $formatter->format(false));
        $formatter = BoolFormatter::make('true', 'false');
        $this->assertSame('true', $formatter->format(true));
        $this->assertSame('false', $formatter->format(false));
        $this->expectException(TypeException::class);
        $formatter->format('');
    }
    
    public function testFormatter1()
    {
        $formatter = BoolFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format(1);
    }
    
    public function testFormatter2()
    {
        $formatter = BoolFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format(1.1);
    }
    
    public function testFormatter3()
    {
        $formatter = BoolFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format([]);
    }
    
    public function testFormatter4()
    {
        $formatter = BoolFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format(new BoolFormatter());
    }
    
    public function testFormatter5()
    {
        $formatter = BoolFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format('jsfgsjfadfasdf');
    }
}