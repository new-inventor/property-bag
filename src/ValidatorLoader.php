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
    /** @var Metadata */
    protected $metadata;
    
    /**
     * ValidatorLoader constructor.
     *
     * @param Metadata $metadata
     */
    public function __construct(Metadata $metadata)
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
        $metadata = $this->metadata->getClassValidationMetadata();
        
        return true;
    }
}