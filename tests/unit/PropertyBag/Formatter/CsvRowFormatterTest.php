<?php

namespace PropertyBag\Formatter;


use TestsPropertyBag\TestIteratorArrayAccess;
use TestsPropertyBag\TestStringable;
use NewInventor\PropertyBag\Formatter\CsvRowFormatter;
use NewInventor\PropertyBag\Formatter\BoolFormatter;
use NewInventor\TypeChecker\Exception\TypeException;

class CsvRowFormatterTest extends \Codeception\Test\Unit
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
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var CsvRowFormatter $formatter */
        $formatter = CsvRowFormatter::make('|');
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertSame('|', $formatter->getSeparator());
        $this->assertNull($formatter->format(null));
        $this->assertSame('', $formatter->format([]));
        $this->assertSame('', $formatter->format([null]));
        $this->assertSame(
            '1234567890|1|"23232"',
            $formatter->format(
                [
                    new TestStringable(),
                    1,
                    '23232',
                ]
            )
        );
        /** @noinspection PhpUndefinedMethodInspection */
        $formatter = CsvRowFormatter::make(
            null,
            null,
            BoolFormatter::make('true', 'false'),
            '|'
        );
        $iterator = new TestIteratorArrayAccess();
        $iterator->parameters[] = '1';
        $iterator->parameters[] = 3;
        $iterator->parameters[] = false;
        $this->assertEquals('"1"|3|"false"', $formatter->format($iterator));
        $this->expectException(TypeException::class);
        $formatter->format(
            [
                new TestStringable(),
                ['sdfasdf', 'asdfasdf'],
                1,
                '23232',
            ]
        );
    }
    
    public function testFormatter1()
    {
        $formatter = CsvRowFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format('');
    }
    
    public function testFormatter2()
    {
        $formatter = CsvRowFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format(false);
    }
    
    public function testFormatter3()
    {
        $formatter = CsvRowFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format(1);
    }
    
    public function testFormatter4()
    {
        $formatter = CsvRowFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format(1.1);
    }
    
    public function testFormatter5()
    {
        $formatter = CsvRowFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format(new CsvRowFormatter());
    }
    
    public function testFormatter6()
    {
        $formatter = CsvRowFormatter::make();
        $this->expectException(TypeException::class);
        $formatter->format([new \stdClass()]);
    }
}