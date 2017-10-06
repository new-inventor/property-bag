<?php

namespace PropertyBag\Formatter;


use NewInventor\DataStructure\Transformer\ArrayToCsvString;
use NewInventor\TypeChecker\Exception\TypeException;
use TestsPropertyBag\TestStringable;

class ArrayToCsvStringTest extends \Codeception\Test\Unit
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
        /** @var ArrayToCsvString $transformer */
        $transformer = ArrayToCsvString::make('|');
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertNull($transformer->transform(null));
        $this->assertSame('', $transformer->transform([]));
        $this->assertSame('', $transformer->transform([null]));
        $this->assertSame(
            '1234567890|1|23232',
            $transformer->transform(
                [
                    new TestStringable(),
                    1,
                    '23232',
                ]
            )
        );
        $this->assertSame(
            '1234567890|1|"232|""\\32"',
            $transformer->transform(
                [
                    new TestStringable(),
                    1,
                    '232|"\\32',
                ]
            )
        );
    }
    
    public function testFormatter1()
    {
        $formatter = ArrayToCsvString::make();
        $this->expectException(TypeException::class);
        $formatter->transform('');
    }
    
    public function testFormatter2()
    {
        $formatter = ArrayToCsvString::make();
        $this->expectException(TypeException::class);
        $formatter->transform(false);
    }
    
    public function testFormatter3()
    {
        $formatter = ArrayToCsvString::make();
        $this->expectException(TypeException::class);
        $formatter->transform(1);
    }
    
    public function testFormatter4()
    {
        $formatter = ArrayToCsvString::make();
        $this->expectException(TypeException::class);
        $formatter->transform(1.1);
    }
    
    public function testFormatter5()
    {
        $formatter = ArrayToCsvString::make();
        $this->expectException(TypeException::class);
        $formatter->transform(new ArrayToCsvString());
    }
    
    public function testFormatter6()
    {
        $formatter = ArrayToCsvString::make();
        $this->expectException(TypeException::class);
        $formatter->transform([new \stdClass()]);
    }
}