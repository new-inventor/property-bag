<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 03.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


class EmptyNormalizer extends AbstractNormalizer
{
    protected function parseInputValue($value)
    {
        if (empty($value)) {
            return null;
        }
    
        return $value;
    }
}