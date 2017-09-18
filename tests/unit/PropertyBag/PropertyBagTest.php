<?php

namespace PropertyBag;


use Codeception\Test\Unit;
use Codeception\Util\Stub;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Filesystem\Filesystem;
use TestsPropertyBag\TestBag;
use NewInventor\PropertyBag\Exception\PropertyNotFoundException;
use NewInventor\PropertyBag\Normalizer\IntNormalizer;
use NewInventor\PropertyBag\Property;
use NewInventor\PropertyBag\PropertyBag;

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
    
    public function testCache()
    {
        $cacheDir = __DIR__ . '/cache';
        $fs = new Filesystem();
        $fs->remove($cacheDir);
        $cacheDriver = Stub::construct(
            FilesystemCache::class,
            ['', 0, $cacheDir],
            [
                'has' => Stub::consecutive(false, true),
            ]
        );
        PropertyBag::setCacheDriver($cacheDriver);
        TestBag::make();
        $this->assertGreaterThan(0, $this->folderSize($cacheDir));
        /** @noinspection SuspiciousAssignmentsInspection */
        TestBag::make();
        $this->assertTrue(is_a(PropertyBag::getCacheDriver(), CacheInterface::class));
        $fs->remove($cacheDir);
        PropertyBag::setCacheDriver();
    }
    
    public function testCacheProperties()
    {
        $cacheDir = __DIR__ . '/cache';
        $fs = new Filesystem();
        $fs->remove($cacheDir);
        $cacheDriver = Stub::construct(
            FilesystemCache::class,
            ['', 0, $cacheDir],
            [
                'has' => Stub::consecutive(false, true, true),
            ]
        );
        PropertyBag::setCacheDriver($cacheDriver);
        $testBag = TestBag::make();
        $beforeSize = $this->folderSize($cacheDir);
        $this->assertGreaterThan(0, $beforeSize);
        /** @noinspection SuspiciousAssignmentsInspection */
        $testBag->addProperty('prop0', Property::make('1')->setNormalizer(IntNormalizer::make()));
        $this->assertEquals(1, $testBag->get('prop0'));
        $afterSize = $this->folderSize($cacheDir);
        $this->assertNotEquals($beforeSize, $afterSize);
        $fs->remove($cacheDir);
        PropertyBag::setCacheDriver();
    }
    
    protected function folderSize($dir)
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->folderSize($each);
        }
        
        return $size;
    }
    
    public function testGet()
    {
        $bag = PropertyBag::make();
        $this->assertEquals([], $bag->toRawArray());
        $bag = TestBag::make();
        $this->assertEquals(null, $bag->get('prop1'));
        $this->assertEquals(1, $bag->get('prop2'));
        $bag->set('prop3', 123);
        $this->assertEquals('123', $bag->get('prop3'));
        $this->expectException(PropertyNotFoundException::class);
        $bag->get('jhdsfkjdhfksdf');
    }
    
    public function testSet()
    {
        $bag = TestBag::make();
        $bag->set('prop1', 123);
        $this->assertEquals(123, $bag->get('prop1'));
        $bag->set('prop2', '1');
        $this->assertEquals('1', $bag->get('prop2'));
        $bag->set('prop3', 123);
        $this->assertEquals('123', $bag->get('prop3'));
        $this->expectException(PropertyNotFoundException::class);
        $bag->set('jhdsfkjdhfksdf', 'sdfhsgdfj');
    }
    
    public function testGetFormatted()
    {
        $bag = TestBag::make();
        $bag->set('prop1', 123);
        $this->assertEquals('123', $bag->getFormatted('prop1'));
        $bag->set('prop2', '1');
        $this->assertEquals('1', $bag->getFormatted('prop2'));
        $bag->set('prop3', '123');
        $this->assertEquals('123', $bag->getFormatted('prop3'));
        $bag->set('prop4', '06.12.2017');
        $this->assertEquals('06.12.2017', $bag->getFormatted('prop4'));
        $this->expectException(PropertyNotFoundException::class);
        $bag->getFormatted('jhdsfkjdhfksdf');
    }
    
    public function testToFormattedArray()
    {
        $bag = TestBag::make();
        $bag->set('prop1', 123);
        $bag->set('prop2', '1');
        $bag->set('prop3', '123');
        $bag->set('prop4', '06.12.2017');
        $this->assertEquals(
            [
                'prop1' => '123',
                'prop2' => '1',
                'prop3' => '123',
                'prop4' => '06.12.2017',
            ],
            $bag->toFormattedArray()
        );
    }
    
    public function testToRawArray()
    {
        $bag = TestBag::make();
        $bag->set('prop1', null);
        $bag->set('prop2', '1');
        $bag->set('prop3', 123);
        $bag->set('prop4', '06.12.2017');
        $array = $bag->toRawArray();
        $this->assertFalse(isset($array['prop1']));
        $this->assertEquals('1', $array['prop2']);
        $this->assertEquals('123', $array['prop3']);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals('06.12.2017', $array['prop4']->format('d.m.Y'));
    }
    
    public function testLoad()
    {
        $bag = TestBag::make()->load(
            [
                'prop1' => 123,
                'prop2' => '1',
                'prop3' => 123,
                'prop4' => '06.12.2017',
            ]
        );
        $array = $bag->toRawArray();
        $this->assertEquals(123, $array['prop1']);
        $this->assertEquals('1', $array['prop2']);
        $this->assertEquals('123', $array['prop3']);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals('06.12.2017', $array['prop4']->format('d.m.Y'));
        $this->expectException(PropertyNotFoundException::class);
        TestBag::make()->load(
            [
                'prop1'    => 123,
                'prop2'    => '1',
                'prop3'    => 123,
                'prop4'    => '06.12.2017',
                'dfsdfsdf' => '06.12.2017',
            ]
        );
    }
}