<?php

use Codeception\Test\Unit;
use NewInventor\DataStructure\Exception\PropertyNotFoundException;
use TestsPropertyBag\Bag;

class PropertyBagTest extends Unit
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
    
    public function testGet()
    {
        $bag = new Bag();
        $this->assertEquals(null, $bag->get('prop1'));
        $this->assertEquals(1, $bag->get('prop2'));
        $bag->set('prop3', 123);
        $this->assertEquals('123', $bag->get('prop3'));
        $this->expectException(PropertyNotFoundException::class);
        $bag->get('jhdsfkjdhfksdf');
    }
    
    public function testSet()
    {
        $bag = new Bag();
        $bag->set('prop1', 123);
        $this->assertEquals(123, $bag->get('prop1'));
        $bag->set('prop2', '1');
        $this->assertEquals('1', $bag->get('prop2'));
        $bag->set('prop3', 123);
        $this->assertEquals('123', $bag->get('prop3'));
        $bag->set('prop5', true);
        $this->expectException(PropertyNotFoundException::class);
        $bag->set('jhdsfkjdhfksdf', 'sdfhsgdfj');
    }
    
    public function testToArray()
    {
        $bag = new Bag();
        $bag->set('prop1', null);
        $bag->set('prop2', '1');
        $bag->set('prop3', 123);
        $bag->set('prop4', \DateTime::createFromFormat('d.m.Y', '06.12.2017'));
        $array = $bag->toArray();
        $this->assertFalse(isset($array['prop1']));
        $this->assertEquals('1', $array['prop2']);
        $this->assertEquals('123', $array['prop3']);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals('06.12.2017', $array['prop4']->format('d.m.Y'));
    }
    
    public function testLoad()
    {
        $bag = (new Bag())->load(
            [
                'prop1' => 123,
                'prop2' => '1',
                'prop3' => 123,
                'prop4' => \DateTime::createFromFormat('d.m.Y', '06.12.2017'),
            ]
        );
        $array = $bag->toArray();
        $this->assertEquals(123, $array['prop1']);
        $this->assertEquals('1', $array['prop2']);
        $this->assertEquals('123', $array['prop3']);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals('06.12.2017', $array['prop4']->format('d.m.Y'));
        $this->expectException(PropertyNotFoundException::class);
        (new Bag())->load(
            [
                'prop1'    => 123,
                'prop2'    => '1',
                'prop3'    => 123,
                'prop4'    => '06.12.2017',
                'dfsdfsdf' => '06.12.2017',
            ]
        );
    }
    
    public function testFailOnFistError()
    {
        $bag = new Bag();
    }
}