<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 10.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


class IntRangeNormalizer extends RangeNormalizer
{
    /**
     * IntRangeNormalizer constructor.
     *
     * @param int|null $min
     * @param int|null $max
     */
    public function __construct(int $min = null, int $max = null)
    {
        parent::__construct($min, $max);
    }
    
    protected function normalizeInputValue($value)
    {
        $value = IntNormalizer::make()->normalize($value);
    
        return parent::normalizeInputValue($value);
    }
    
    public static function asString(...$config): string
    {
        $values = [];
        if(isset($config[0])) {
            $values[] = (string)$config[0];
        }
        if(isset($config[1])) {
            $values[] = (string)$config[1];
        }
        return parent::asString() . '_' . implode('_', $values);
    }
    
    public function __toString(): string
    {
        return static::asString($this->min, $this->max);
    }
}