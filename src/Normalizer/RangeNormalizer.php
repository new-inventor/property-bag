<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 03.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


class RangeNormalizer extends AbstractNormalizer
{
    /** @var mixed|null */
    protected $min;
    /** @var mixed|null */
    protected $max;
    /** @var callable|null */
    protected $compareFunction;
    
    /**
     * IntRangeNormalizer constructor.
     *
     * @param mixed|null    $min
     * @param mixed|null    $max
     * @param callable|null $compareFunction
     */
    public function __construct($min = null, $max = null, callable $compareFunction = null)
    {
        $this->min = $min;
        $this->max = $max;
        $this->compareFunction = $compareFunction;
    }
    
    protected function normalizeInputValue($value)
    {
        if ($this->compareFunction !== null) {
            return $this->compareWithFunction($value);
        }
        
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
    
    protected function compareWithFunction($value)
    {
        $func = $this->compareFunction;
        if ((int)$func($value, $this->min) === -1) {
            $value = $this->min;
        }
        if ((int)$func($value, $this->max) === 1) {
            $value = $this->max;
        }
        
        return $value;
    }
}