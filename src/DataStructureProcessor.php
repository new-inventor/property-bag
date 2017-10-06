<?php
/**
 * Project: property-bag
 * User: george
 * Date: 06.10.17
 */

namespace NewInventor\DataStructure;


use NewInventor\DataStructure\Transformer\DataStructure\StructureTransformerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

class DataStructureProcessor
{
    /** @var CacheInterface */
    protected $cacheDriver;
    /** @var DataStructureMetadata */
    protected $metadata;
    protected $class;
    
    /**
     * DataStructureProcessor constructor.
     *
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }
    
    /**
     * @param CacheItemPoolInterface $cacheDriver
     *
     * @return $this
     */
    public function setCacheDriver(CacheItemPoolInterface $cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
        
        return $this;
    }
    
    /**
     * @param DataStructureMetadata $metadata
     *
     * @return $this
     */
    public function setMetadata(DataStructureMetadata $metadata)
    {
        $this->metadata = $metadata;
        
        return $this;
    }
    
    public function construct(array $parameters = [])
    {
    
    }
    
    public function transform(&$dataStructure, StructureTransformerInterface $transformer)
    {
        $transformer->transform($dataStructure);
    }
    
    public function transformProperty(&$dataStructure, StructureTransformerInterface $transformer)
    {
    
    }
}