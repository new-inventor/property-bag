<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 13.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\PropertyBag\Currencies;
use NewInventor\PropertyBag\Exception\NormalizeException;

class CurrencyNormalizer extends EnumNormalizer
{
    /**
     * CurrencyNormalizer constructor.
     */
    public function __construct()
    {
        parent::__construct(
            array_merge(Currencies::$active, Currencies::$old, Currencies::$notIso),
            StringNormalizer::make()
        );
    }
    
    protected function normalizeInputValue($value)
    {
        
        if ($this->normalizer !== null) {
            $value = $this->normalizer->normalize($value);
        }
        if ($value === null) {
            return null;
        }
        $value = strtoupper($value);
        if (array_key_exists($value, $this->availableValues)) {
            return $value;
        }
        
        $values = implode(', ', array_flip($this->availableValues));
        throw new NormalizeException(
            __CLASS__,
            $value,
            "Property should be string and one of $values or same in lower case"
        );
    }
}