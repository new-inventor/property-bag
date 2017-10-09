<?php

use NewInventor\DataStructure\ValidatorLoader;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class ValidatorLoaderTest extends \Codeception\Test\Unit
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
        $metadata = new ClassMetadata(ValidatorLoader::class);
        $loader = new ValidatorLoader($metadata);
        $metadata1 = new ClassMetadata(ClassMetadata::class);
        $this->assertTrue($loader->loadClassMetadata($metadata1));
        $this->assertSame('ClassMetadata', $metadata1->defaultGroup);
        $this->assertSame($metadata->members, $metadata1->members);
        $this->assertSame($metadata->properties, $metadata1->properties);
        $this->assertSame($metadata->getters, $metadata1->getters);
        $this->assertSame($metadata->groupSequence, $metadata1->groupSequence);
        $this->assertSame($metadata->groupSequenceProvider, $metadata1->groupSequenceProvider);
        $this->assertSame($metadata->traversalStrategy, $metadata1->traversalStrategy);
    }
}