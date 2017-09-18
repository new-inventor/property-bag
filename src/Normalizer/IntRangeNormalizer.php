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
}