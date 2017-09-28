<?php
/**
 * Project: property-bag
 * User: george
 * Date: 28.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


class ScreamingCaseNormalizer extends StringNormalizer
{
    protected function normalizeInputValue($value)
    {
        $value = parent::normalizeInputValue($value);
        $value = preg_replace(['/[^_a-zA-Z0-9]+/', '/(?:_$|^_)/'], ['_', ''], $value);
        $value = preg_replace_callback('/_?(?:[A-Z]+|\d+)/', function ($matches) {
            return '_' . $matches[0];
        }, $value);
        $value = preg_replace('/_+/', '_', $value);
        return strtoupper($value);
    }
}