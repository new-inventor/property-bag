<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 09.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\PropertyBag\Exception\NormalizeException;
use NewInventor\TypeChecker\TypeChecker;

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
        TypeChecker::check($availableValues)->inner()->tscalar()->fail();
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
    
    public static function asString(...$config): string
    {
        $segments = [];
        foreach($config[0] as $value){
            if(is_bool($value)){
                $segments[] = $value ? 'true': 'false';
            }else{
                $segments[] = (string)$value;
            }
        }
        if(isset($config[1])) {
            $segments[] = (string)$config[1];
        }
        return parent::asString(). '_' . implode('_', $segments);
    }
    
    public function __toString(): string
    {
        return static::asString($this->availableValues, $this->normalizer);
    }
}