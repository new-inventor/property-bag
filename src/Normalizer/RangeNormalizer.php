<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 03.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


abstract class RangeNormalizer extends AbstractNormalizer
{
    /** @var mixed|null */
    protected $min;
    /** @var mixed|null */
    protected $max;
    
    /**
     * IntRangeNormalizer constructor.
     *
     * @param mixed|null    $min
     * @param mixed|null    $max
     * @param callable|null $compareFunction
     */
    public function __construct($min = null, $max = null)
    {
        $this->min = $min;
        $this->max = $max;
    }
    
    protected function normalizeInputValue($value)
    {
        return $this->compareDefault($value);
    }
    
    protected function compareDefault($value)
    {
        if ($this->max !== null && $value > $this->max) {
            $value = $this->max;
        }
        if ($this->min !== null && $value < $this->min) {
            $value = $this->min;
        }
        
        return $value;
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