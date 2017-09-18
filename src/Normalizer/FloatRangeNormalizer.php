<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 10.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;



class FloatRangeNormalizer extends RangeNormalizer
{
    /**
     * IntRangeNormalizer constructor.
     *
     * @param float|null $min
     * @param float|null $max
     */
    public function __construct(float $min = null, float $max = null)
    {
        parent::__construct($min, $max);
    }
    
    protected function normalizeInputValue($value)
    {
        $value = FloatNormalizer::make()->normalize($value);
    
        return parent::normalizeInputValue($value);
    }
}