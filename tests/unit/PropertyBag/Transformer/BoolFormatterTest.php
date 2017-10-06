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
        $formatter = BoolFormatter::make(1, 0);
        $this->assertSame(1, $formatter->format(true));
        $this->assertSame(0, $formatter->format(false));
        $this->assertSame(1, $formatter->format('dfsdfsdf'));
        $this->assertSame(0, $formatter->format(''));
        $this->assertSame(1, $formatter->format(1));
        $this->assertSame(0, $formatter->format(0));
        $this->assertSame(1, $formatter->format(1.1));
        $this->assertSame(0, $formatter->format(0.0));
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
}