<?php
/**
 * Project: property-bag
 * User: george
 * Date: 12.10.17
 */

namespace NewInventor\PropertyBag\Metadata;


use NewInventor\DataStructure\Metadata\Factory as BaseFactory;
use NewInventor\DataStructure\Metadata\Loader;
use NewInventor\DataStructure\Metadata\MetadataInterface;

class Factory extends BaseFactory
{
    /**
     * @param string $class
     *
     * @return MetadataInterface
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \InvalidArgumentException
     */
    protected function constructMetadata(string $class): MetadataInterface
    {
        $config = new Configuration();
        $metadata = new Metadata($class, $this->validationCache);
        $parser = new Parser($config);
        $loader = new Loader($this->basePath, $parser, $this->baseNamespace);
        $loader->loadMetadata($metadata);
        
        return $metadata;
    }
}