<?php
/**
 * Project: property-bag
 * User: george
 * Date: 12.10.17
 */

namespace NewInventor\PropertyBag\Metadata;


use NewInventor\DataStructure\Metadata\Factory as BaseFactory;
use NewInventor\DataStructure\Metadata\MetadataInterface;
use NewInventor\DataStructure\PropertiesTransformer;
use NewInventor\PropertyBag\PropertyBag;

class Factory extends BaseFactory
{
    /**
     * @param string $class
     *
     * @return MetadataInterface
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \InvalidArgumentException
     */
    protected function constructMetadata($class): MetadataInterface
    {
        $metadata = new Metadata($class);
        $this->loader->load($metadata);
        if (
            $metadata->parent !== PropertyBag::class &&
            strpos($metadata->parent, $this->loader->getBaseNamespace()) !== false
        ) {
            /** @var Metadata $parentMetadata */
            $parentMetadata = $this->constructMetadata($metadata->parent);
            /**
             * @var string                $group
             * @var PropertiesTransformer $transformer
             */
            foreach ($parentMetadata->transformers as $group => $transformer) {
                $parentTransformers = $transformer->getTransformers();
                foreach ($parentTransformers as $propertyName => $propertyTransformer) {
                    if (!isset($metadata->transformers[$group])) {
                        $metadata->transformers[$group] = new PropertiesTransformer();
                    }
                    if ($metadata->transformers[$group]->getTransformer($propertyName) === null) {
                        $metadata->transformers[$group]->setTransformer($propertyName, $propertyTransformer);
                    }
                }
            }
        }
        
        return $metadata;
    }
}