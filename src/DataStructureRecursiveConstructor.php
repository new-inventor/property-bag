<?php
/**
 * Project: property-bag
 * User: george
 * Date: 09.10.17
 */

namespace NewInventor\DataStructure;


class DataStructureRecursiveConstructor
{
    /** @var MetadataLoader */
    protected $metadataLoader;
    
    /**
     * DataStructureRecursiveConstructor constructor.
     *
     * @param MetadataLoader $metadataLoader
     */
    public function __construct(MetadataLoader $metadataLoader)
    {
        $this->metadataLoader = $metadataLoader;
    }
    
    /**
     * @param string $className
     * @param array  $properties
     *
     * @return mixed
     * @throws \LogicException
     */
    public function construct(string $className, array $properties = [])
    {
        $metadata = $this->metadataLoader->loadMetadataFor($className);
        $transformedProperties = $metadata->getTransformer('load')->transform($properties);
        $constructedNested = $this->constructNested($metadata->getNested(), $transformedProperties);
        $transformedProperties = array_merge($transformedProperties, $constructedNested);
        if (
            in_array(Loadable::class, class_implements($className), true) ||
            in_array(DataStructureInterface::class, class_implements($className), true)
        ) {
            /** @var Loadable $dataStructure */
            $dataStructure = new $className();
            $dataStructure->load($transformedProperties);
            
            return $dataStructure;
        }
        throw new \LogicException(
            'Data structure class must implement ' .
            Loadable::class .
            ' or ' .
            DataStructureInterface::class
        );
    }
    
    /**
     * @param array $nestedConfigs
     * @param array $properties
     *
     * @return array
     * @throws \LogicException
     */
    protected function constructNested(array $nestedConfigs, array $properties): array
    {
        $res = [];
        if (count($nestedConfigs) > 0) {
            foreach ($nestedConfigs as $propertyName => $config) {
                $res[$propertyName] = $this->constructConcreteNested($properties[$propertyName], $config);
            }
        }
        
        return $res;
    }
    
    /**
     * @param $propertyValue
     * @param $config
     *
     * @return mixed
     * @throws \LogicException
     */
    protected function constructConcreteNested($propertyValue, $config)
    {
        if (isset($nestedConfig['metadata'])) {
            $metadataLoader = new MetadataLoader($config['metadata']['path'], $config['metadata']['baseNamespace']);
            $nestedConstructor = new self($metadataLoader);
            
            return $nestedConstructor->construct($config['class'], $propertyValue);
        }
        $nestedConstructor = new self($this->metadataLoader);
        
        return $nestedConstructor->construct($config['class'], $propertyValue);
    }
}