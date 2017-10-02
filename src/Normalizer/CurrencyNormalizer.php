<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 13.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\PropertyBag\Accessory\Currencies;
use NewInventor\PropertyBag\Exception\NormalizeException;

class CurrencyNormalizer extends EnumNormalizer
{
    /**
     * CurrencyNormalizer constructor.
     * @throws \NewInventor\TypeChecker\Exception\TypeException
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
    
    public static function asString(...$config): string
    {
        return static::class;
    }
}