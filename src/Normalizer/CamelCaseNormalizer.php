<?php
/**
 * Project: property-bag
 * User: george
 * Date: 28.09.17
 */

namespace NewInventor\PropertyBag\Normalizer;


class CamelCaseNormalizer extends StringNormalizer
{
    protected function normalizeInputValue($value)
    {
        $value = parent::normalizeInputValue($value);
        $value = preg_replace_callback(
            '/[^a-zA-Z0-9]+([a-zA-Z])/',
            function ($matches) {
                return strtoupper($matches[1]);
            },
            $value
        );
        
        return preg_replace('/[^a-zA-Z0-9]/', '', $value);
    }
}