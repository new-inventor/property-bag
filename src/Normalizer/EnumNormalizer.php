<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 09.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\PropertyBag\Exception\NormalizeException;

class EnumNormalizer extends AbstractNormalizer
{
    /** @var array */
    protected $availableValues;
    /** @var NormalizerInterface */
    protected $normalizer;
    
    /**
     * EnumNormalizer constructor.
     *
     * @param array      $availableValues
     * @param NormalizerInterface $normalizer
     *
     * @throws \NewInventor\TypeChecker\Exception\TypeException
     */
    public function __construct(array $availableValues, NormalizerInterface $normalizer = null)
    {
        $this->availableValues = array_flip(array_unique($availableValues));
        $this->normalizer = $normalizer;
    }
    
    public function normalize($value)
    {
        $this->validateInputTypes($value);
        $value = $this->parseInputValue($value);
        
        return $this->normalizeInputValue($value);
    }
    
    protected function normalizeInputValue($value)
    {
        if ($this->normalizer !== null) {
            $value = $this->normalizer->normalize($value);
        }
        if ($value === null) {
            return null;
        }
        if (array_key_exists($value, $this->availableValues)) {
            return $value;
        }
        
        $values = implode(', ', array_flip($this->availableValues));
        throw new NormalizeException(
            __CLASS__,
            $value,
            "Property should be string and one of $values"
        );
    }
}