<?php

use NewInventor\DataStructure\MetadataLoader;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Filesystem\Filesystem;

class MetadataLoaderTest extends \Codeception\Test\Unit
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
    public function testSomeFeature()
    {
        $loader = new MetadataLoader(__DIR__ . '/data', 'TestsDataStructure');
        $cache = new FilesystemAdapter('', 0, __DIR__ . '/var');
        $loader->setCacheDriver($cache);
        $this->assertSame(__DIR__ . '/data', $loader->getPath());
        $this->assertSame(__DIR__ . '/data/TestBag.yml', $loader->getFilePath('TestsDataStructure\TestBag'));
        $this->assertSame('TestsDataStructure', $loader->getBaseNamespace());
        $loader->loadMetadataFor('TestsDataStructure\TestBag');
        $this->assertTrue(is_dir(__DIR__ . '/var'));
        $loader->loadMetadataFor('TestsDataStructure\TestBag');
        $fileSystem = new Filesystem();
        $fileSystem->remove(__DIR__ . '/var');
        $this->expectException(\InvalidArgumentException::class);
        $loader->loadMetadataFor('not\existing\class');
    }
    
    public function test1()
    {
        $this->expectException(\RuntimeException::class);
        $loader = new MetadataLoader(__DIR__ . '/data/TestBag.yml');
        $loader->loadMetadataFor('TestBag');
    }
    
    public function test2()
    {
        $this->expectException(\RuntimeException::class);
        $loader = new MetadataLoader(__DIR__ . '/data', 'TestsDataStructure');
        $loader->loadMetadata();
    }
    
    public function test3()
    {
        $loader = new MetadataLoader(__DIR__ . '/data/TestBag.yml');
        $loader->loadMetadata();
        $this->assertSame(__DIR__ . '/data/TestBag.yml', $loader->getPath());
    }
}