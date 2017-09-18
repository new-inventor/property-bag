<?php

namespace PropertyBag\Formatter;


use NewInventor\PropertyBag\Formatter\DateTimeFormatter;
use NewInventor\TypeChecker\Exception\TypeException;

class DateTimeFormatterTest extends \Codeception\Test\Unit
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
        $formatter = DateTimeFormatter::make('d.m.Y');
        $this->assertSame('06.09.2017', $formatter->format(\DateTime::createFromFormat('U', '1504715838')));
        $this->assertNull($formatter->format(null));
        $this->expectException(TypeException::class);
        $formatter->format('06.09.2017');
    }
    
    public function testFormatter1()
    {
        $formatter = DateTimeFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format(1);
    }
    
    public function testFormatter2()
    {
        $formatter = DateTimeFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format(1.1);
    }
    
    public function testFormatter3()
    {
        $formatter = DateTimeFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format([]);
    }
    
    public function testFormatter4()
    {
        $formatter = DateTimeFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format(new DateTimeFormatter());
    }
    
    public function testFormatter5()
    {
        $formatter = DateTimeFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format('jsfgsjfadfasdf');
    }
    
    public function testFormatter6()
    {
        $formatter = DateTimeFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format(true);
    }
}