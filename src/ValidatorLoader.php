<?php
/**
 * Project: property-bag
 * User: george
 * Date: 06.10.17
 */

namespace NewInventor\DataStructure;


use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\LoaderInterface;

class ValidatorLoader implements LoaderInterface
{
    /** @var ClassMetadata */
    protected $metadata;
    
    /**
     * ValidatorLoader constructor.
     *
     * @param ClassMetadata $metadata
     */
    public function __construct(ClassMetadata $metadata)
    {
        $this->metadata = $metadata;
    }
    
    /**
     * Loads validation metadata into a {@link ClassMetadata} instance.
     *
     * @param ClassMetadata $metadata The metadata to load
     *
     * @return bool Whether the loader succeeded
     */
    public function loadClassMetadata(ClassMetadata $metadata)
    {
        if ($metadata->name !== $this->metadata->name) {
            return true;
        }
        $metadata->defaultGroup = $this->metadata->defaultGroup;
        $metadata->members = $this->metadata->members;
        $metadata->properties = $this->metadata->properties;
        $metadata->getters = $this->metadata->getters;
        $metadata->groupSequence = $this->metadata->groupSequence;
        $metadata->groupSequenceProvider = $this->metadata->groupSequenceProvider;
        $metadata->traversalStrategy = $this->metadata->traversalStrategy;
        
        return true;
    }
}