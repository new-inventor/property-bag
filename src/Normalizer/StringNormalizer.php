<?php
/**
 * Project: TP messaging service
 * User: george
 * Date: 10.08.17
 */

namespace NewInventor\PropertyBag\Normalizer;


use NewInventor\TypeChecker\TypeChecker;

class StringNormalizer extends AbstractNormalizer
{
    protected function validateInputTypes($value)
    {
        TypeChecker::check($value)->tnull()->tscalar()->callback(
            function ($value) {
                return is_object($value) && method_exists($value, '__toString');
            }
        )->fail();
    }
    
    protected function normalizeInputValue($value)
    {
        return (string)$value;
    }
}